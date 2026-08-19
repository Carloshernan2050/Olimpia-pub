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
            [
                'primer_nombre' => 'Ana',
                'segundo_nombre' => 'Lucia',
                'primer_apellido' => 'Perez',
                'segundo_apellido' => 'Lopez',
                'correo' => 'cliente@olimpia.com',
                'rol' => 'cliente',
            ],
            [
                'primer_nombre' => 'Luis',
                'segundo_nombre' => 'Andres',
                'primer_apellido' => 'Gomez',
                'segundo_apellido' => 'Martinez',
                'correo' => 'empleado@olimpia.com',
                'rol' => 'empleado',
            ],
            [
                'primer_nombre' => 'Maria',
                'segundo_nombre' => 'Elena',
                'primer_apellido' => 'Ruiz',
                'segundo_apellido' => 'Garcia',
                'correo' => 'admin@olimpia.com',
                'rol' => 'administrador',
            ],
            [
                'primer_nombre' => 'Carlos',
                'segundo_nombre' => 'Jose',
                'primer_apellido' => 'Diaz',
                'segundo_apellido' => 'Hernandez',
                'correo' => 'super@olimpia.com',
                'rol' => 'superadministrador',
            ],
        ];

        foreach ($usuarios as $usuario) {
            if ($this->usuarioRepository->findByCorreo($usuario['correo']) !== null) {
                continue;
            }

            $rol = $this->rolRepository->findByNombre($usuario['rol']);

            if ($rol === null) {
                continue;
            }

            $this->usuarioRepository->create([
                'primer_nombre' => $usuario['primer_nombre'],
                'segundo_nombre' => $usuario['segundo_nombre'],
                'primer_apellido' => $usuario['primer_apellido'],
                'segundo_apellido' => $usuario['segundo_apellido'],
                'correo' => $usuario['correo'],
                'contrasena' => 'password',
                'estado' => 'activo',
                'id_rol' => $rol->id_rol,
            ]);
        }
    }
}
