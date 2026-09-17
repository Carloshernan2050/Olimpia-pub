<?php

namespace App\DTOs\Dashboard;

use App\Enums\TipoMesa;

final readonly class TipoMesaDatos
{
    /**
     * Chip de filtro del catálogo de mesas.
     */
    public function __construct(
        public TipoMesa $tipo,
        public string $icono,
        public string $etiqueta,
    ) {}

    /**
     * El chip seleccionado invita a volver al catálogo completo.
     */
    public function etiquetaFiltro(bool $seleccionado): string
    {
        return $seleccionado ? 'Todos' : $this->etiqueta;
    }

    /**
     * @return list<self>
     */
    public static function catalogo(): array
    {
        return array_map(
            fn (TipoMesa $tipo): self => new self($tipo, $tipo->icono(), $tipo->etiqueta()),
            TipoMesa::cases(),
        );
    }

    /**
     * Tipos que una mesa individual puede guardar.
     *
     * @return list<self>
     */
    public static function persistibles(): array
    {
        return array_values(array_filter(
            self::catalogo(),
            fn (self $tipo): bool => $tipo->tipo->esPersistible(),
        ));
    }
}
