<?php

namespace App\DTOs\Autenticacion;

final readonly class NombrePersona
{
    /**
     * Agrupa primer y segundo nombre y apellido.
     */
    public function __construct(
        public string $primerNombre,
        public ?string $segundoNombre,
        public string $primerApellido,
        public ?string $segundoApellido,
    ) {}

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'primer_nombre' => $this->primerNombre,
            'segundo_nombre' => $this->segundoNombre,
            'primer_apellido' => $this->primerApellido,
            'segundo_apellido' => $this->segundoApellido,
        ];
    }
}
