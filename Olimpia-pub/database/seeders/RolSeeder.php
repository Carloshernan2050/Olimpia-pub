<?php

namespace Database\Seeders;

use App\Contracts\Repositories\RolRepositoryInterface;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Inyecta el repositorio de roles.
     */
    public function __construct(
        private readonly RolRepositoryInterface $rolRepository
    ) {
    }

    /**
     * Crea los roles del sistema si aún no existen.
     */
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
