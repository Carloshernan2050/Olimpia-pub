<?php

namespace Tests\Feature;

use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
    }

    public function test_muestra_la_pagina_de_inicio(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_muestra_el_formulario_de_registro(): void
    {
        $this->get('/registro')->assertOk();
    }

    public function test_muestra_el_formulario_de_inicio_de_sesion(): void
    {
        $this->get('/iniciar-sesion')->assertOk();
    }

    public function test_registra_un_usuario_cliente(): void
    {
        $respuesta = $this->post('/registro', [
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'contrasena_confirmation' => 'password1',
        ]);

        $respuesta->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('usuario', [
            'correo' => 'ana@olimpia.com',
            'primer_nombre' => 'Ana',
        ]);
    }

    public function test_no_registra_si_el_correo_ya_existe(): void
    {
        $this->post('/registro', [
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'contrasena_confirmation' => 'password1',
        ])->assertRedirect('/');

        $this->post('/cerrar-sesion');

        $this->from('/registro')->post('/registro', [
            'primer_nombre' => 'Luis',
            'primer_apellido' => 'Gomez',
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'contrasena_confirmation' => 'password1',
        ])->assertSessionHasErrors('correo');
    }

    public function test_inicia_sesion_con_credenciales_validas(): void
    {
        $this->post('/registro', [
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'contrasena_confirmation' => 'password1',
        ]);

        $this->post('/cerrar-sesion');

        $this->post('/iniciar-sesion', [
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
    }

    public function test_rechaza_credenciales_invalidas(): void
    {
        $this->from('/iniciar-sesion')->post('/iniciar-sesion', [
            'correo' => 'nadie@olimpia.com',
            'contrasena' => 'password1',
        ])->assertSessionHasErrors('correo');

        $this->assertGuest();
    }
}
