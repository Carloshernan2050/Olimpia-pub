<?php

namespace App\DTOs\Dashboard;

use App\Enums\AccionHistorial;

final readonly class RegistrarHistorialDatos
{
    /**
     * Entrada lista para persistir en historial.
     */
    public function __construct(
        public AccionHistorial $accion,
        public int $idUsuario,
        public string $descripcion,
    ) {}

    /**
     * Cierre de un pedido activo de una mesa.
     */
    public static function terminarPedido(int $idUsuario, int $numeroMesa, string $totalFormateado): self
    {
        return new self(
            AccionHistorial::TerminarPedido,
            $idUsuario,
            'Pedido de Mesa '.$numeroMesa.' cerrado. Total '.$totalFormateado.'.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function paraCrear(): array
    {
        return [
            'accion' => $this->accion->value,
            'fecha' => now(),
            'descripcion' => $this->descripcion,
            'id_usuario' => $this->idUsuario,
        ];
    }
}
