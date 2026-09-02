<?php

namespace App\Contracts\Repositories;

use App\DTOs\Dashboard\FiltroInventarioDatos;
use App\Models\Producto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductoRepositoryInterface
{
    /**
     * Crea un producto con los datos recibidos.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Producto;

    /**
     * Actualiza un producto existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Producto $producto, array $data): Producto;

    /**
     * Elimina un producto.
     */
    public function delete(Producto $producto): void;

    /**
     * Busca un producto por su nombre.
     */
    public function findByNombre(string $nombre): ?Producto;

    /**
     * Busca un producto por su identificador.
     */
    public function findById(int $id): ?Producto;

    /**
     * Productos filtrados y paginados para el catálogo de inventario.
     *
     * @return LengthAwarePaginator<int, Producto>
     */
    public function filtrar(FiltroInventarioDatos $filtro, int $porPagina = 8): LengthAwarePaginator;

    /**
     * Todos los productos ordenados por nombre.
     *
     * @return Collection<int, Producto>
     */
    public function todos(): Collection;

    /**
     * Totales de productos, unidades y alertas de stock.
     *
     * @return array{productos: int, unidades: int, bajo: int, agotados: int}
     */
    public function resumenStock(int $umbralBajo): array;
}
