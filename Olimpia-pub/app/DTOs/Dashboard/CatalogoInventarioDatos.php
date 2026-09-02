<?php

namespace App\DTOs\Dashboard;

final readonly class CatalogoInventarioDatos
{
    /**
     * @param  list<ProductoInventarioDatos>  $productos
     * @param  list<CategoriaInventarioDatos>  $categorias
     * @param  list<ProductoInventarioOpcionDatos>  $opcionesProducto
     */
    public function __construct(
        public array $productos,
        public ResumenInventarioDatos $resumen,
        public PaginacionInventarioDatos $paginacion,
        public array $categorias,
        public array $opcionesProducto,
    ) {}

    /**
     * Indica si la página actual tiene al menos un producto.
     */
    public function tieneProductos(): bool
    {
        return $this->productos !== [];
    }

    /**
     * Recorre los productos de la página en el orden del repositorio.
     *
     * @return list<ProductoInventarioDatos>
     */
    public function enOrden(): array
    {
        return $this->productos;
    }
}
