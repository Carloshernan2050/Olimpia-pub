<?php

namespace App\DTOs\Dashboard;

use App\Models\Promocion;

final readonly class PeriodoPromocion
{
    /**
     * Rango de vigencia de una promoción.
     */
    public function __construct(
        public string $inicio,
        public string $fin,
    ) {}

    /**
     * Construye el periodo a partir del modelo persistido.
     */
    public static function fromModel(Promocion $promocion): self
    {
        return new self(
            $promocion->fecha_inicio->toDateString(),
            $promocion->fecha_fin->toDateString(),
        );
    }

    /**
     * @return array{fecha_inicio: string, fecha_fin: string}
     */
    public function paraPersistir(): array
    {
        return [
            'fecha_inicio' => $this->inicio,
            'fecha_fin' => $this->fin,
        ];
    }
}
