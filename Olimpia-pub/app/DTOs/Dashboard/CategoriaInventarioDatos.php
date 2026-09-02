<?php

namespace App\DTOs\Dashboard;

use App\Models\Categoria;

final readonly class CategoriaInventarioDatos
{
    /**
     * Categoría para el select del filtro y el formulario.
     */
    public function __construct(
        public int $id,
        public string $nombre,
    ) {}

    /**
     * Construye el DTO a partir del modelo persistido.
     */
    public static function fromModel(Categoria $categoria): self
    {
        return new self(
            (int) $categoria->id_categoria,
            $categoria->nombre,
        );
    }
}
