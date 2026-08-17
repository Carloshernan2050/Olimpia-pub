<?php

namespace Database\Seeders;

use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Inyecta los repositorios de usuario y rol.
     */
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RolRepositoryInterface $rolRepository
    ) {}

    /**
     * Crea usuarios de ejemplo para cada rol si aún no existen.
     */
    public function run(): void
    {
        $usuarios = [
            ['Ana', 'Lucia', 'Cliente', 'Perez', 'cliente@olimpia.com', 'cliente'],
            ['Luis', 'Andres', 'Empleado', 'Gomez', 'empleado@olimpia.com', 'empleado'],
            ['Maria', 'Elena', 'Admin', 'Ruiz', 'admin@olimpia.com', 'administrador'],
            ['Carlos', 'Jose', 'Super', 'Diaz', 'super@olimpia.com', 'superadministrador'],
        ];

        foreach ($usuarios as [$primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $correo, $nombreRol]) {
            if ($this->usuarioRepository->findByCorreo($correo) !== null) {
                continue;
            }

            $rol = $this->rolRepository->findByNombre($nombreRol);

            if ($rol === null) {
                continue;
            }

            $this->usuarioRepository->create([
                'primer_nombre' => $primerNombre,
                'segundo_nombre' => $segundoNombre,
                'primer_apellido' => $primerApellido,
                'segundo_apellido' => $segundoApellido,
                'correo' => $correo,
                'contrasena' => 'password',
                'estado' => 'activo',
                'id_rol' => $rol->id_rol,
            ]);
        }
    }
}
