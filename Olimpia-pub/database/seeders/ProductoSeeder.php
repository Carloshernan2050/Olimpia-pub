<?php

namespace Database\Seeders;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Inyecta los repositorios de producto y categoría.
     */
    public function __construct(
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly CategoriaRepositoryInterface $categoriaRepository
    ) {
    }

    /**
     * Crea productos de ejemplo asociados a sus categorías.
     */
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Limonada',
                'descripcion' => 'Limonada natural',
                'precio' => 8.50,
                'stock' => 50,
                'estado' => 'activo',
                'categoria' => 'Bebidas',
            ],
            [
                'nombre' => 'Hamburguesa clásica',
                'descripcion' => 'Carne, queso y vegetales',
                'precio' => 35.00,
                'stock' => 30,
                'estado' => 'activo',
                'categoria' => 'Comidas',
            ],
            [
                'nombre' => 'Brownie',
                'descripcion' => 'Brownie de chocolate',
                'precio' => 15.00,
                'stock' => 20,
                'estado' => 'activo',
                'categoria' => 'Postres',
            ],
        ];

        foreach ($productos as $producto) {
            if ($this->productoRepository->findByNombre($producto['nombre']) !== null) {
                continue;
            }

            $categoria = $this->categoriaRepository->findByNombre($producto['categoria']);

            if ($categoria === null) {
                continue;
            }

            $this->productoRepository->create([
                'nombre' => $producto['nombre'],
                'descripcion' => $producto['descripcion'],
                'precio' => $producto['precio'],
                'stock' => $producto['stock'],
                'estado' => $producto['estado'],
                'id_categoria' => $categoria->id_categoria,
            ]);
        }
    }
}
