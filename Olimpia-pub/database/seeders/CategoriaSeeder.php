<?php

namespace Database\Seeders;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function __construct(
        private readonly CategoriaRepositoryInterface $categoriaRepository
    ) {
    }

    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes'],
            ['nombre' => 'Comidas', 'descripcion' => 'Platos principales'],
            ['nombre' => 'Postres', 'descripcion' => 'Dulces y postres'],
        ];

        foreach ($categorias as $categoria) {
            if ($this->categoriaRepository->findByNombre($categoria['nombre']) !== null) {
                continue;
            }

            $this->categoriaRepository->create($categoria);
        }
    }
}
