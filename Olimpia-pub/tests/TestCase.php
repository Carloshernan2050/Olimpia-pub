<?php

namespace Tests;

use App\Models\Rol;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    /**
     * Prepara cada prueba sin exigir el manifiesto de Vite.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * Envía el formulario de registro del usuario de prueba.
     */
    protected function registrarUsuarioCliente(string $correo = 'ana@olimpia.com'): TestResponse
    {
        return $this->post('/registro', [
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => $correo,
            'contrasena' => 'password1',
            'contrasena_confirmation' => 'password1',
        ]);
    }

    /**
     * Deja a un usuario cliente autenticado.
     */
    protected function autenticar(string $correo = 'ana@olimpia.com'): void
    {
        $this->registrarUsuarioCliente($correo)->assertRedirect(route('dashboard'));
    }

    /**
     * Deja autenticado a un usuario con el rol indicado.
     */
    protected function autenticarConRol(string $nombreRol, string $correo = 'ana@olimpia.com'): void
    {
        $this->autenticar($correo);

        $usuario = auth()->user();
        $rol = Rol::query()->where('nombre_rol', $nombreRol)->firstOrFail();

        $usuario->forceFill(['id_rol' => $rol->id_rol])->save();
        $usuario->unsetRelation('rol');
    }
}
