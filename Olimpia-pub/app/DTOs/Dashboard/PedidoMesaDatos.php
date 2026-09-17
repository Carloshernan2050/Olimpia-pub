<?php

namespace App\DTOs\Dashboard;

use App\Models\Pedido;

final readonly class PedidoMesaDatos
{
    /**
     * @param  list<LineaPedidoMesaDatos>  $lineas
     */
    public function __construct(
        public int $id,
        public string $total,
        public array $lineas,
    ) {}

    /**
     * Pedido activo de la mesa, con sus productos.
     */
    public static function fromModel(Pedido $pedido): self
    {
        $lineas = $pedido->detalles
            ->map(fn ($detalle): LineaPedidoMesaDatos => LineaPedidoMesaDatos::fromModel($detalle))
            ->values()
            ->all();

        return new self(
            (int) $pedido->id_pedido,
            (string) $pedido->total,
            $lineas,
        );
    }

    public function tieneLineas(): bool
    {
        return $this->lineas !== [];
    }

    /**
     * @return list<LineaPedidoMesaDatos>
     */
    public function enOrden(): array
    {
        return $this->lineas;
    }

    /**
     * Nombres visibles en la celda del tablero.
     *
     * @return list<string>
     */
    public function nombresParaCelda(int $limite = 4): array
    {
        return array_slice(
            array_map(fn (LineaPedidoMesaDatos $linea): string => $linea->nombre, $this->lineas),
            0,
            $limite,
        );
    }

    public function totalFormateado(): string
    {
        return '$ '.number_format((float) $this->total, 2, ',', '.');
    }
}
