<?php

namespace App\DTOs\Dashboard;

final readonly class FiltroMenuDatos
{
    /**
     * Filtro del menú: búsqueda por nombre y categoría de inventario.
     */
    public function __construct(
        public ?string $busqueda,
        public ?int $idCategoria,
    ) {}

    /**
     * Filtro inicial: todo el catálogo, sin búsqueda.
     */
    public static function predeterminado(): self
    {
        return new self(null, null);
    }

    /**
     * Interpreta los parámetros de consulta del menú.
     */
    public static function fromInput(?string $busqueda, mixed $categoria): self
    {
        $idCategoria = is_numeric($categoria) && (int) $categoria > 0
            ? (int) $categoria
            : null;

        return new self(self::texto($busqueda), $idCategoria);
    }

    /**
     * Indica si el usuario aplicó búsqueda o categoría.
     */
    public function estaActivo(): bool
    {
        return $this->busqueda !== null || $this->idCategoria !== null;
    }

    /**
     * Query string para conservar el filtro en otros enlaces.
     *
     * @return array<string, int|string>
     */
    public function query(): array
    {
        return array_filter([
            'busqueda' => $this->busqueda,
            'categoria' => $this->idCategoria,
        ], fn (int|string|null $valor): bool => $valor !== null && $valor !== '');
    }

    /**
     * Query del filtro sustituyendo la categoría (o quitándola).
     *
     * @return array<string, int|string>
     */
    public function queryConCategoria(?int $idCategoria): array
    {
        return array_filter([
            'busqueda' => $this->busqueda,
            'categoria' => $idCategoria,
        ], fn (int|string|null $valor): bool => $valor !== null && $valor !== '');
    }

    private static function texto(?string $valor): ?string
    {
        $texto = trim((string) $valor);

        return $texto === '' ? null : mb_substr($texto, 0, 150);
    }
}
