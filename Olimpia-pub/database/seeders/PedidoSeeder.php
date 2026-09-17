<?php

namespace Database\Seeders;

use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use App\Exceptions\Mesa\PedidoActivoYaExisteException;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    /**
     * Inyecta mesas, productos y la gestión de pedidos.
     */
    public function __construct(
        private readonly MesaRepositoryInterface $mesaRepository,
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly GestionPedidosServiceInterface $gestionPedidos,
    ) {}

    /**
     * Deja pedidos activos de ejemplo en algunas mesas.
     */
    public function run(): void
    {
        foreach ($this->pedidosDeEjemplo() as $numeroMesa => $lineas) {
            $mesa = $this->mesaRepository->findByNumero($numeroMesa);

            if ($mesa === null || $lineas === []) {
                continue;
            }

            try {
                $this->gestionPedidos->crearActivo(new GuardarPedidoMesaDatos(
                    (int) $mesa->id_mesa,
                    $lineas,
                ));
            } catch (PedidoActivoYaExisteException) {
                continue;
            }
        }
    }

    /**
     * @return array<int, list<array{id_producto: int, cantidad: int}>>
     */
    private function pedidosDeEjemplo(): array
    {
        return array_filter([
            2 => $this->lineas([
                ['Limonada', 2],
                ['Hamburguesa clásica', 1],
            ]),
            6 => $this->lineas([
                ['Brownie', 1],
            ]),
            8 => $this->lineas([
                ['Limonada', 1],
                ['Hamburguesa clásica', 2],
                ['Brownie', 1],
            ]),
        ]);
    }

    /**
     * @param  list<array{0: string, 1: int}>  $productos
     * @return list<array{id_producto: int, cantidad: int}>
     */
    private function lineas(array $productos): array
    {
        $lineas = [];

        foreach ($productos as [$nombre, $cantidad]) {
            $producto = $this->productoRepository->findByNombre($nombre);

            if ($producto === null) {
                continue;
            }

            $lineas[] = [
                'id_producto' => (int) $producto->id_producto,
                'cantidad' => $cantidad,
            ];
        }

        return $lineas;
    }
}
