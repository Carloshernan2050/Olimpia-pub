<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\DTOs\Dashboard\FiltroInventarioDatos;
use App\Enums\EstadoStockInventario;
use App\Models\Producto;
use App\Support\Dashboard\UmbralStockInventario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentProductoRepository extends EloquentRepository implements ProductoRepositoryInterface
{
    /**
     * Crea un producto con los datos recibidos.
     */
    public function create(array $data): Producto
    {
        /** @var Producto */
        return $this->createModel($data);
    }

    /**
     * Actualiza un producto existente.
     */
    public function update(Producto $producto, array $data): Producto
    {
        $producto->update($data);

        return $producto->fresh() ?? $producto;
    }

    /**
     * Elimina un producto.
     */
    public function delete(Producto $producto): void
    {
        $producto->promociones()->detach();
        $producto->delete();
    }

    /**
     * Busca un producto por su nombre.
     */
    public function findByNombre(string $nombre): ?Producto
    {
        /** @var Producto|null */
        return $this->findFirstBy('nombre', $nombre);
    }

    /**
     * Busca un producto por su identificador.
     */
    public function findById(int $id): ?Producto
    {
        /** @var Producto|null */
        return $this->findFirstBy('id_producto', $id);
    }

    /**
     * Productos filtrados y paginados para el catálogo de inventario.
     *
     * @return LengthAwarePaginator<int, Producto>
     */
    public function filtrar(FiltroInventarioDatos $filtro, int $porPagina = 8): LengthAwarePaginator
    {
        $consulta = $this->newQuery()->with('categoria');

        if ($filtro->busqueda !== null) {
            $consulta->where('nombre', 'like', $this->patronBusqueda($filtro->busqueda));
        }

        if ($filtro->idCategoria !== null) {
            $consulta->where('id_categoria', $filtro->idCategoria);
        }

        $this->aplicarEstadoStock($consulta, $filtro->estadoStock);

        return $consulta
            ->orderBy('nombre')
            ->paginate($porPagina, ['*'], 'page', $filtro->pagina)
            ->withQueryString();
    }

    /**
     * Todos los productos ordenados por nombre.
     *
     * @return Collection<int, Producto>
     */
    public function todos(): Collection
    {
        return $this->newQuery()
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Totales de productos, unidades y alertas de stock.
     *
     * @return array{productos: int, unidades: int, bajo: int, agotados: int}
     */
    public function resumenStock(int $umbralBajo = UmbralStockInventario::BAJO): array
    {
        return [
            'productos' => (int) $this->newQuery()->count(),
            'unidades' => (int) $this->newQuery()->sum('stock'),
            'bajo' => (int) $this->newQuery()
                ->where('stock', '>', 0)
                ->where('stock', '<=', $umbralBajo)
                ->count(),
            'agotados' => (int) $this->newQuery()->where('stock', '<=', 0)->count(),
        ];
    }

    /**
     * @return class-string<Producto>
     */
    protected function modelClass(): string
    {
        return Producto::class;
    }

    private function aplicarEstadoStock(Builder $consulta, ?EstadoStockInventario $estado): void
    {
        if ($estado === null) {
            return;
        }

        match ($estado) {
            EstadoStockInventario::Agotado => $consulta->where('stock', '<=', 0),
            EstadoStockInventario::Bajo => $consulta
                ->where('stock', '>', 0)
                ->where('stock', '<=', UmbralStockInventario::BAJO),
            EstadoStockInventario::Disponible => $consulta->where('stock', '>', UmbralStockInventario::BAJO),
        };
    }

    private function patronBusqueda(string $busqueda): string
    {
        $escapado = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $busqueda);

        return '%'.$escapado.'%';
    }
}
