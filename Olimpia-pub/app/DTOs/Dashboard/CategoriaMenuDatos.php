<?php

namespace App\DTOs\Dashboard;

use App\Models\Categoria;

final readonly class CategoriaMenuDatos
{
    /**
     * Categoría del menú, con el icono de la carta.
     */
    public function __construct(
        public int $id,
        public string $nombre,
        public string $icono,
    ) {}

    /**
     * Construye el DTO a partir de la categoría de inventario.
     */
    public static function fromModel(Categoria $categoria, string $icono): self
    {
        return new self(
            (int) $categoria->id_categoria,
            $categoria->nombre,
            $icono,
        );
    }
}
