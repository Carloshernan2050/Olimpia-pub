<?php

namespace App\Services;

use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Services\GestionInventarioServiceInterface;
use App\DTOs\Dashboard\GuardarMovimientoInventarioDatos;
use App\DTOs\Dashboard\GuardarProductoInventarioDatos;
use App\DTOs\Dashboard\MovimientoInventarioGestionDatos;
use App\DTOs\Dashboard\ProductoInventarioDatos;
use App\Enums\TipoMovimientoInventario;
use App\Exceptions\Inventario\MovimientoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoConPedidosException;
use App\Exceptions\Inventario\ProductoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoNombreDuplicadoException;
use App\Exceptions\Inventario\StockInsuficienteException;
use App\Models\MovimientoInventario;
use App\Models\Producto;

class GestionInventarioService implements GestionInventarioServiceInterface
{
    /**
     * Inyecta los repositorios de producto y movimiento.
     */
    public function __construct(
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly MovimientoInventarioRepositoryInterface $movimientoRepository,
    ) {}

    /**
     * Persiste un movimiento nuevo y actualiza el stock del producto.
     */
    public function crear(
        GuardarMovimientoInventarioDatos $datos,
        int $idUsuario,
    ): MovimientoInventarioGestionDatos {
        $producto = $this->obtenerProducto($datos->idProducto);
        $this->aplicarMovimiento($producto, $datos->tipo, $datos->cantidad);

        return MovimientoInventarioGestionDatos::fromModel(
            $this->movimientoRepository->create($datos->paraCrear($idUsuario))
        );
    }

    /**
     * Revierte el movimiento anterior, aplica el nuevo y lo persiste.
     */
    public function actualizar(
        int $id,
        GuardarMovimientoInventarioDatos $datos,
    ): MovimientoInventarioGestionDatos {
        $actual = $this->obtenerMovimiento($id);
        $productoAnterior = $this->obtenerProducto((int) $actual->id_producto);
        $this->revertirMovimiento(
            $productoAnterior,
            TipoMovimientoInventario::tryFrom((string) $actual->tipo_movimiento) ?? TipoMovimientoInventario::Entrada,
            (int) $actual->cantidad,
        );

        $producto = $this->obtenerProducto($datos->idProducto);
        $this->aplicarMovimiento($producto, $datos->tipo, $datos->cantidad);

        return MovimientoInventarioGestionDatos::fromModel(
            $this->movimientoRepository->update($actual, $datos->paraActualizar())
        );
    }

    /**
     * Elimina un movimiento y revierte su efecto en el stock.
     */
    public function eliminar(int $id): void
    {
        $actual = $this->obtenerMovimiento($id);
        $producto = $this->obtenerProducto((int) $actual->id_producto);
        $this->revertirMovimiento(
            $producto,
            TipoMovimientoInventario::tryFrom((string) $actual->tipo_movimiento) ?? TipoMovimientoInventario::Entrada,
            (int) $actual->cantidad,
        );
        $this->movimientoRepository->delete($actual);
    }

    /**
     * Persiste un producto nuevo y registra la entrada inicial si hay stock.
     */
    public function crearProducto(
        GuardarProductoInventarioDatos $datos,
        int $idUsuario,
    ): ProductoInventarioDatos {
        if ($this->productoRepository->findByNombre($datos->nombre) !== null) {
            throw new ProductoNombreDuplicadoException;
        }

        $producto = $this->productoRepository->create($datos->paraCrear());

        if ($datos->stock > 0) {
            $this->movimientoRepository->create([
                'tipo_movimiento' => TipoMovimientoInventario::Entrada->value,
                'cantidad' => $datos->stock,
                'fecha' => now(),
                'id_producto' => $producto->id_producto,
                'id_usuario' => $idUsuario,
            ]);
        }

        $producto->loadMissing('categoria');

        return ProductoInventarioDatos::fromModel($producto);
    }

    /**
     * Devuelve el movimiento si existe.
     */
    public function buscar(int $id): ?MovimientoInventarioGestionDatos
    {
        $movimiento = $this->movimientoRepository->findById($id);

        return $movimiento === null ? null : MovimientoInventarioGestionDatos::fromModel($movimiento);
    }

    /**
     * Devuelve el producto si existe.
     */
    public function buscarProducto(int $id): ?ProductoInventarioDatos
    {
        $producto = $this->productoRepository->findById($id);

        if ($producto === null) {
            return null;
        }

        $producto->loadMissing('categoria');

        return ProductoInventarioDatos::fromModel($producto);
    }

    /**
     * Elimina un producto y sus movimientos si no tiene pedidos.
     */
    public function eliminarProducto(int $id): void
    {
        $producto = $this->obtenerProducto($id);

        if ($producto->detallesPedido()->exists()) {
            throw new ProductoConPedidosException;
        }

        $this->movimientoRepository->eliminarDeProducto($id);
        $this->productoRepository->delete($producto);
    }

    /**
     * Recorre los movimientos recientes para el listado del modal.
     *
     * @return list<MovimientoInventarioGestionDatos>
     */
    public function listar(): array
    {
        return $this->movimientoRepository
            ->recientes()
            ->map(fn (MovimientoInventario $movimiento): MovimientoInventarioGestionDatos => MovimientoInventarioGestionDatos::fromModel($movimiento))
            ->values()
            ->all();
    }

    /**
     * Recorre los movimientos de un producto.
     *
     * @return list<MovimientoInventarioGestionDatos>
     */
    public function listarDeProducto(int $idProducto): array
    {
        return $this->movimientoRepository
            ->porProducto($idProducto)
            ->map(fn (MovimientoInventario $movimiento): MovimientoInventarioGestionDatos => MovimientoInventarioGestionDatos::fromModel($movimiento))
            ->values()
            ->all();
    }

    /**
     * Carga el producto o lanza si no existe.
     */
    private function obtenerProducto(int $id): Producto
    {
        $producto = $this->productoRepository->findById($id);

        if ($producto === null) {
            throw new ProductoInventarioNoEncontradoException;
        }

        return $producto;
    }

    /**
     * Carga el movimiento o lanza si no existe.
     */
    private function obtenerMovimiento(int $id): MovimientoInventario
    {
        $movimiento = $this->movimientoRepository->findById($id);

        if ($movimiento === null) {
            throw new MovimientoInventarioNoEncontradoException;
        }

        return $movimiento;
    }

    /**
     * Suma o resta stock según el tipo de movimiento.
     */
    private function aplicarMovimiento(Producto $producto, TipoMovimientoInventario $tipo, int $cantidad): void
    {
        $this->cambiarStock($producto, $this->delta($tipo, $cantidad));
    }

    /**
     * Deshace el efecto de un movimiento ya persistido.
     */
    private function revertirMovimiento(Producto $producto, TipoMovimientoInventario $tipo, int $cantidad): void
    {
        $this->cambiarStock($producto, -$this->delta($tipo, $cantidad));
    }

    /**
     * Variación de stock: positiva en entradas y negativa en salidas.
     */
    private function delta(TipoMovimientoInventario $tipo, int $cantidad): int
    {
        return $tipo === TipoMovimientoInventario::Entrada ? $cantidad : -$cantidad;
    }

    /**
     * Persiste el nuevo stock si no queda negativo.
     */
    private function cambiarStock(Producto $producto, int $delta): void
    {
        $nuevo = (int) $producto->stock + $delta;

        if ($nuevo < 0) {
            throw new StockInsuficienteException;
        }

        $this->productoRepository->update($producto, ['stock' => $nuevo]);
    }
}
