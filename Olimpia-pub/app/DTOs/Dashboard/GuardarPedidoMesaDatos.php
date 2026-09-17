<?php

namespace App\DTOs\Dashboard;

use App\Enums\EstadoPedido;

final readonly class GuardarPedidoMesaDatos
{
    /**
     * @param  list<array{id_producto: int, cantidad: int, precio?: string}>  $lineas
     */
    public function __construct(
        public int $idMesa,
        public array $lineas,
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     */
    public static function fromValidated(array $datos): self
    {
        return new self(
            (int) $datos['id_mesa'],
            $datos['lineas'] ?? [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function paraCrear(string $total): array
    {
        return [
            'fecha' => now(),
            'estado' => EstadoPedido::Activo->value,
            'total' => $total,
            'id_mesa' => $this->idMesa,
        ];
    }
}
