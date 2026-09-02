<?php

namespace App\DTOs\Dashboard;

use App\Enums\EstadoStockInventario;

final readonly class FiltroInventarioDatos
{
    /**
     * Filtro del catálogo: búsqueda, categoría, estado de stock y página.
     */
    public function __construct(
        public ?string $busqueda,
        public ?int $idCategoria,
        public ?EstadoStockInventario $estadoStock,
        public int $pagina,
    ) {}

    /**
     * Filtro inicial: sin búsqueda ni selects, primera página.
     */
    public static function predeterminado(): self
    {
        return new self(null, null, null, 1);
    }

    /**
     * Interpreta los parámetros de consulta del inventario.
     */
    public static function fromInput(
        ?string $busqueda,
        mixed $categoria,
        ?string $estado,
        mixed $pagina,
    ): self {
        $idCategoria = is_numeric($categoria) && (int) $categoria > 0
            ? (int) $categoria
            : null;

        $paginaActual = is_numeric($pagina) && (int) $pagina > 0
            ? (int) $pagina
            : 1;

        return new self(
            self::texto($busqueda),
            $idCategoria,
            self::estado($estado),
            $paginaActual,
        );
    }

    /**
     * Indica si el usuario aplicó búsqueda o algún select.
     */
    public function estaActivo(): bool
    {
        return $this->busqueda !== null
            || $this->idCategoria !== null
            || $this->estadoStock !== null;
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
            'estado' => $this->estadoStock?->value,
        ], fn (int|string|null $valor): bool => $valor !== null && $valor !== '');
    }

    private static function texto(?string $valor): ?string
    {
        $texto = trim((string) $valor);

        return $texto === '' ? null : mb_substr($texto, 0, 150);
    }

    private static function estado(?string $valor): ?EstadoStockInventario
    {
        return EstadoStockInventario::tryFrom((string) $valor);
    }
}
