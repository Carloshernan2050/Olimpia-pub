<?php

namespace Database\Seeders;

use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RolRepositoryInterface $rolRepository
    ) {}

    public function run(): void
    {
        $usuarios = [
            [
                'primer_nombre' => 'Ana',
                'segundo_nombre' => 'Lucia',
                'primer_apellido' => 'Cliente',
                'segundo_apellido' => 'Perez',
                'correo' => 'cliente@olimpia.com',
                'contrasena' => 'password',
                'estado' => 'activo',
                'rol' => 'cliente',
            ],
            [
                'primer_nombre' => 'Luis',
                'segundo_nombre' => 'Andres',
                'primer_apellido' => 'Empleado',
                'segundo_apellido' => 'Gomez',
                'correo' => 'empleado@olimpia.com',
                'contrasena' => 'password',
                'estado' => 'activo',
                'rol' => 'empleado',
            ],
            [
                'primer_nombre' => 'Maria',
                'segundo_nombre' => 'Elena',
                'primer_apellido' => 'Admin',
                'segundo_apellido' => 'Ruiz',
                'correo' => 'admin@olimpia.com',
                'contrasena' => 'password',
                'estado' => 'activo',
                'rol' => 'administrador',
            ],
            [
                'primer_nombre' => 'Carlos',
                'segundo_nombre' => 'Jose',
                'primer_apellido' => 'Super',
                'segundo_apellido' => 'Diaz',
                'correo' => 'super@olimpia.com',
                'contrasena' => 'password',
                'estado' => 'activo',
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
                'contrasena' => $usuario['contrasena'],
                'estado' => $usuario['estado'],
                'id_rol' => $rol->id_rol,
            ]);
        }
    }
}
