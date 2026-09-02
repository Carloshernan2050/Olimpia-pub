<?php

namespace App\Services;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Services\CatalogoInventarioServiceInterface;
use App\DTOs\Dashboard\CatalogoInventarioDatos;
use App\DTOs\Dashboard\CategoriaInventarioDatos;
use App\DTOs\Dashboard\FiltroInventarioDatos;
use App\DTOs\Dashboard\PaginacionInventarioDatos;
use App\DTOs\Dashboard\ProductoInventarioDatos;
use App\DTOs\Dashboard\ProductoInventarioOpcionDatos;
use App\DTOs\Dashboard\ResumenInventarioDatos;
use App\Models\Categoria;
use App\Models\Producto;
use App\Support\Dashboard\UmbralStockInventario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CatalogoInventarioService implements CatalogoInventarioServiceInterface
{
    /**
     * Inyecta los repositorios del catálogo de inventario.
     */
    public function __construct(
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly CategoriaRepositoryInterface $categoriaRepository,
        private readonly MovimientoInventarioRepositoryInterface $movimientoRepository,
    ) {}

    /**
     * Convierte los productos filtrados en filas del catálogo.
     */
    public function obtenerCatalogo(?FiltroInventarioDatos $filtro = null): CatalogoInventarioDatos
    {
        $filtro ??= FiltroInventarioDatos::predeterminado();
        $paginador = $this->productoRepository->filtrar($filtro);
        $resumen = $this->productoRepository->resumenStock(UmbralStockInventario::BAJO);

        return new CatalogoInventarioDatos(
            $paginador
                ->getCollection()
                ->map(fn (Producto $producto): ProductoInventarioDatos => ProductoInventarioDatos::fromModel($producto))
                ->values()
                ->all(),
            new ResumenInventarioDatos(
                $resumen['productos'],
                $this->movimientoRepository->contar(),
                $resumen['bajo'],
                $resumen['agotados'],
            ),
            $this->paginacion($paginador),
            $this->categoriaRepository
                ->todas()
                ->map(fn (Categoria $categoria): CategoriaInventarioDatos => CategoriaInventarioDatos::fromModel($categoria))
                ->values()
                ->all(),
            $this->productoRepository
                ->todos()
                ->map(fn (Producto $producto): ProductoInventarioOpcionDatos => ProductoInventarioOpcionDatos::fromModel($producto))
                ->values()
                ->all(),
        );
    }

    /**
     * Extrae los enlaces de página del paginador.
     *
     * @param  LengthAwarePaginator<int, Producto>  $paginador
     */
    private function paginacion(LengthAwarePaginator $paginador): PaginacionInventarioDatos
    {
        return new PaginacionInventarioDatos(
            $paginador->currentPage(),
            $paginador->lastPage(),
            $paginador->previousPageUrl(),
            $paginador->nextPageUrl(),
        );
    }
}
