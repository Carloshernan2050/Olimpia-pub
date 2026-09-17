<?php

namespace App\DTOs\Dashboard;

final readonly class GuardarLiberarGruposDatos
{
    /**
     * @param  list<int>  $idsGrupos
     */
    public function __construct(
        public array $idsGrupos,
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     */
    public static function fromValidated(array $datos): self
    {
        $ids = array_values(array_unique(array_map(
            intval(...),
            $datos['grupos'] ?? [],
        )));
        sort($ids);

        return new self($ids);
    }
}
