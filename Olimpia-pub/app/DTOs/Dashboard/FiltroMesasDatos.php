<?php

namespace App\DTOs\Dashboard;

use App\Enums\TipoMesa;

final readonly class FiltroMesasDatos
{
    /**
     * Filtro del catálogo de mesas por tipo.
     */
    public function __construct(
        public ?TipoMesa $tipo,
    ) {}

    /**
     * Catálogo completo, sin tipo seleccionado.
     */
    public static function predeterminado(): self
    {
        return new self(null);
    }

    /**
     * Interpreta el tipo de la consulta.
     */
    public static function fromInput(mixed $tipo): self
    {
        $valor = is_string($tipo) ? TipoMesa::tryFrom($tipo) : null;

        return new self($valor);
    }

    /**
     * Indica si hay un tipo aplicado.
     */
    public function estaActivo(): bool
    {
        return $this->tipo !== null;
    }

    /**
     * Query string para conservar el filtro.
     *
     * @return array<string, string>
     */
    public function query(): array
    {
        return $this->filtrarQuery([
            'tipo' => $this->tipo?->value,
        ]);
    }

    /**
     * Query sustituyendo el tipo, o el catálogo completo.
     *
     * @return array<string, string>
     */
    public function queryConTipo(?TipoMesa $tipo): array
    {
        return $this->filtrarQuery([
            'tipo' => $tipo?->value,
        ]);
    }

    /**
     * @param  array<string, string|null>  $query
     * @return array<string, string>
     */
    private function filtrarQuery(array $query): array
    {
        return array_filter(
            $query,
            fn (?string $valor): bool => $valor !== null && $valor !== '',
        );
    }
}
