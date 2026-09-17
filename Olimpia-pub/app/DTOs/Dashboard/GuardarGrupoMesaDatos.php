<?php

namespace App\DTOs\Dashboard;

final readonly class GuardarGrupoMesaDatos
{
    /**
     * @param  list<int>  $idsMesas
     */
    public function __construct(
        public array $idsMesas,
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     */
    public static function fromValidated(array $datos): self
    {
        $ids = array_values(array_unique(array_map(
            intval(...),
            $datos['mesas'] ?? [],
        )));
        sort($ids);

        return new self($ids);
    }
}
