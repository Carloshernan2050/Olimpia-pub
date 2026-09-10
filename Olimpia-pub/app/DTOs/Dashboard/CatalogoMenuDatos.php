<?php

namespace App\DTOs\Dashboard;

final readonly class CatalogoMenuDatos
{
    /**
     * @param  list<ProductoMenuDatos>  $productos
     * @param  list<CategoriaMenuDatos>  $categorias
     */
    public function __construct(
        public array $productos,
        public array $categorias,
    ) {}

    /**
     * Indica si el menú tiene al menos un producto.
     */
    public function tieneProductos(): bool
    {
        return $this->productos !== [];
    }

    /**
     * Recorre los productos en el orden del repositorio.
     *
     * @return list<ProductoMenuDatos>
     */
    public function enOrden(): array
    {
        return $this->productos;
    }
}
