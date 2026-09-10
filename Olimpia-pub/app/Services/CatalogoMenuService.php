<?php

namespace App\Services;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Services\CatalogoMenuServiceInterface;
use App\DTOs\Dashboard\CatalogoMenuDatos;
use App\DTOs\Dashboard\CategoriaMenuDatos;
use App\DTOs\Dashboard\FiltroMenuDatos;
use App\DTOs\Dashboard\ProductoMenuDatos;
use App\Models\Categoria;
use App\Models\Producto;
use App\Support\Dashboard\IconoCategoriaMenu;

class CatalogoMenuService implements CatalogoMenuServiceInterface
{
    /**
     * Inyecta los repositorios de inventario y el mapa de iconos.
     */
    public function __construct(
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly CategoriaRepositoryInterface $categoriaRepository,
        private readonly IconoCategoriaMenu $iconoCategoria,
    ) {}

    /**
     * Convierte los productos activos del inventario en tarjetas del menú.
     */
    public function obtenerCatalogo(?FiltroMenuDatos $filtro = null): CatalogoMenuDatos
    {
        $filtro ??= FiltroMenuDatos::predeterminado();

        return new CatalogoMenuDatos(
            $this->productoRepository
                ->paraMenu($filtro)
                ->map(fn (Producto $producto): ProductoMenuDatos => ProductoMenuDatos::fromModel($producto))
                ->values()
                ->all(),
            $this->categorias(),
        );
    }

    /**
     * Categorías de inventario, en el orden de las chips de la carta.
     *
     * @return list<CategoriaMenuDatos>
     */
    private function categorias(): array
    {
        return $this->categoriaRepository
            ->todas()
            ->sortBy(fn (Categoria $categoria): string => sprintf(
                '%03d-%s',
                $this->iconoCategoria->peso($categoria->nombre),
                $categoria->nombre,
            ))
            ->map(
                fn (Categoria $categoria): CategoriaMenuDatos => CategoriaMenuDatos::fromModel(
                    $categoria,
                    $this->iconoCategoria->para($categoria->nombre),
                )
            )
            ->values()
            ->all();
    }
}
