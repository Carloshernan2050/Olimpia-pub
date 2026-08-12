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
    ) {
    }

    public function run(): void
    {
        $usuarios = [
            [
                'nombre' => 'Ana',
                'apellido' => 'Cliente',
                'correo' => 'cliente@olimpia.com',
                'contrasena' => 'password',
                'estado' => 'activo',
                'rol' => 'cliente',
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Empleado',
                'correo' => 'empleado@olimpia.com',
                'contrasena' => 'password',
                'estado' => 'activo',
                'rol' => 'empleado',
            ],
            [
                'nombre' => 'Maria',
                'apellido' => 'Admin',
                'correo' => 'admin@olimpia.com',
                'contrasena' => 'password',
                'estado' => 'activo',
                'rol' => 'administrador',
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Super',
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
                'nombre' => $usuario['nombre'],
                'apellido' => $usuario['apellido'],
                'correo' => $usuario['correo'],
                'contrasena' => $usuario['contrasena'],
                'estado' => $usuario['estado'],
                'id_rol' => $rol->id_rol,
            ]);
        }
    }
}
