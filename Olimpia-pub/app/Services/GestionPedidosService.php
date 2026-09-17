<?php

namespace App\Services;

use App\Contracts\Repositories\DetallePedidoRepositoryInterface;
use App\Contracts\Repositories\HistorialRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\PedidoRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use App\DTOs\Dashboard\PedidoMesaDatos;
use App\DTOs\Dashboard\RegistrarHistorialDatos;
use App\Enums\EstadoPedido;
use App\Exceptions\Inventario\ProductoInventarioNoEncontradoException;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Exceptions\Mesa\PedidoActivoNoEncontradoException;
use App\Exceptions\Mesa\PedidoActivoYaExisteException;
use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Database\ConnectionInterface;

class GestionPedidosService implements GestionPedidosServiceInterface
{
    /**
     * Inyecta mesas, pedidos, historial y la conexión para el alta atómica.
     */
    public function __construct(
        private readonly MesaRepositoryInterface $mesaRepository,
        private readonly PedidoRepositoryInterface $pedidoRepository,
        private readonly DetallePedidoRepositoryInterface $detallePedidoRepository,
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly HistorialRepositoryInterface $historialRepository,
        private readonly ConnectionInterface $conexion,
    ) {}

    /**
     * Abre un pedido en la mesa solo si no hay otro activo.
     */
    public function crearActivo(GuardarPedidoMesaDatos $datos): PedidoMesaDatos
    {
        $this->asegurarMesa($datos->idMesa);

        if ($this->pedidoRepository->activoDeMesa($datos->idMesa) !== null) {
            throw new PedidoActivoYaExisteException;
        }

        $pedido = $this->conexion->transaction(
            fn (): Pedido => $this->persistir($datos),
        );

        return PedidoMesaDatos::fromModel($pedido);
    }

    /**
     * Crea el pedido o suma renglones al activo de la mesa.
     */
    public function registrar(GuardarPedidoMesaDatos $datos): PedidoMesaDatos
    {
        $this->asegurarMesa($datos->idMesa);
        $activo = $this->pedidoRepository->activoDeMesa($datos->idMesa);

        $pedido = $this->conexion->transaction(
            fn (): Pedido => $activo === null
                ? $this->persistir($datos)
                : $this->persistirAgregado($activo, $datos),
        );

        return PedidoMesaDatos::fromModel($pedido);
    }

    /**
     * Cierra el pedido activo de la mesa y lo deja en el historial.
     */
    public function terminar(int $idMesa, int $idUsuario): void
    {
        $mesa = $this->mesaDe($idMesa);
        $pedido = $this->pedidoRepository->activoDeMesa($idMesa);

        if ($pedido === null) {
            throw new PedidoActivoNoEncontradoException;
        }

        $registro = RegistrarHistorialDatos::terminarPedido(
            $idUsuario,
            (int) $mesa->numero_mesa,
            PedidoMesaDatos::fromModel($pedido)->totalFormateado(),
        );

        $this->conexion->transaction(function () use ($pedido, $registro): void {
            $this->pedidoRepository->update($pedido, [
                'estado' => EstadoPedido::Cerrado->value,
            ]);
            $this->historialRepository->create($registro->paraCrear());
        });
    }

    /**
     * Crea el pedido y sus renglones en una sola transacción.
     */
    private function persistir(GuardarPedidoMesaDatos $datos): Pedido
    {
        $renglones = $this->renglones($datos);
        $total = array_sum(array_column($renglones, 'subtotal'));
        $pedido = $this->pedidoRepository->create(
            $datos->paraCrear(number_format($total, 2, '.', '')),
        );

        foreach ($renglones as $renglon) {
            $this->detallePedidoRepository->create([
                ...$renglon,
                'id_pedido' => $pedido->id_pedido,
            ]);
        }

        return $this->pedidoConDetalles((int) $pedido->id_pedido, $pedido);
    }

    /**
     * Agrega productos al pedido activo y actualiza el total.
     */
    private function persistirAgregado(Pedido $pedido, GuardarPedidoMesaDatos $datos): Pedido
    {
        $renglones = $this->renglones($datos);
        $agregado = array_sum(array_map(
            fn (array $renglon): float => (float) $renglon['subtotal'],
            $renglones,
        ));

        foreach ($renglones as $renglon) {
            $this->detallePedidoRepository->create([
                ...$renglon,
                'id_pedido' => $pedido->id_pedido,
            ]);
        }

        $this->pedidoRepository->update($pedido, [
            'total' => number_format((float) $pedido->total + $agregado, 2, '.', ''),
        ]);

        return $this->pedidoConDetalles((int) $pedido->id_pedido, $pedido);
    }

    private function asegurarMesa(int $idMesa): void
    {
        $this->mesaDe($idMesa);
    }

    private function mesaDe(int $idMesa): Mesa
    {
        $mesa = $this->mesaRepository->findById($idMesa);

        if ($mesa === null) {
            throw new MesaNoEncontradaException;
        }

        return $mesa;
    }

    private function pedidoConDetalles(int $id, Pedido $reserva): Pedido
    {
        return $this->pedidoRepository->findById($id) ?? $reserva;
    }

    /**
     * @return list<array{cantidad: int, precio_unitario: string, subtotal: string, id_producto: int}>
     */
    private function renglones(GuardarPedidoMesaDatos $datos): array
    {
        $renglones = [];

        foreach ($datos->lineas as $linea) {
            $producto = $this->productoRepository->findById((int) $linea['id_producto']);

            if ($producto === null) {
                throw new ProductoInventarioNoEncontradoException;
            }

            $cantidad = max(1, (int) $linea['cantidad']);
            $precio = number_format((float) ($linea['precio'] ?? $producto->precio), 2, '.', '');
            $subtotal = number_format((float) $precio * $cantidad, 2, '.', '');

            $renglones[] = [
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal,
                'id_producto' => (int) $producto->id_producto,
            ];
        }

        return $renglones;
    }
}
