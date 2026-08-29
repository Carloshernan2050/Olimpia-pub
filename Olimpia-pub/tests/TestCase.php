<?php

namespace Tests;

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
}
