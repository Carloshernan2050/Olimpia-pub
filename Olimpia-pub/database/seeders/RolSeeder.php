<?php

namespace Database\Seeders;

use App\Contracts\Repositories\RolRepositoryInterface;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function __construct(
        private readonly RolRepositoryInterface $rolRepository
    ) {
    }

    public function run(): void
    {
        $roles = [
            'cliente',
            'empleado',
            'administrador',
            'superadministrador',
        ];

        foreach ($roles as $nombreRol) {
            if ($this->rolRepository->findByNombre($nombreRol) !== null) {
                continue;
            }

            $this->rolRepository->create([
                'nombre_rol' => $nombreRol,
            ]);
        }
    }
}
