<?php

namespace Tests\Feature;

use App\Models\Usuario;
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

        $respuesta->assertRedirect(route('dashboard'));
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
        ])->assertRedirect(route('dashboard'));

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
        ])->assertRedirect(route('dashboard'));

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

    public function test_registra_con_nombres_opcionales_y_responde_json(): void
    {
        $respuesta = $this->postJson('/registro', [
            'primer_nombre' => 'Ana',
            'segundo_nombre' => 'Maria',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Lopez',
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'contrasena_confirmation' => 'password1',
        ]);

        $respuesta->assertCreated()
            ->assertJsonPath('usuario.correo', 'ana@olimpia.com')
            ->assertJsonPath('usuario.segundo_nombre', 'Maria')
            ->assertJsonPath('usuario.rol', 'cliente');

        $this->assertAuthenticated();
    }

    public function test_inicia_sesion_por_json(): void
    {
        $this->registrarUsuario();
        $this->post('/cerrar-sesion');

        $this->postJson('/iniciar-sesion', [
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
        ])->assertOk()
            ->assertJsonPath('mensaje', 'Sesión iniciada correctamente.')
            ->assertJsonPath('usuario.correo', 'ana@olimpia.com');

        $this->assertAuthenticated();
    }

    public function test_cierra_sesion_por_web_y_json(): void
    {
        $this->registrarUsuario();

        $this->post('/cerrar-sesion')
            ->assertRedirect('/')
            ->assertSessionHas('exito');

        $this->assertGuest();

        $this->registrarUsuario('luis@olimpia.com');

        $this->postJson('/cerrar-sesion')
            ->assertOk()
            ->assertJsonPath('mensaje', 'Sesión cerrada correctamente.');

        $this->assertGuest();
    }

    public function test_rechaza_usuario_inactivo(): void
    {
        $this->registrarUsuario();
        $this->post('/cerrar-sesion');

        Usuario::query()->where('correo', 'ana@olimpia.com')->update(['estado' => 'inactivo']);

        $this->from('/iniciar-sesion')->post('/iniciar-sesion', [
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
        ])->assertSessionHasErrors('correo');

        $this->assertGuest();
    }

    public function test_valida_el_formulario_de_registro(): void
    {
        $this->from('/registro')->post('/registro', [
            'primer_nombre' => '',
            'primer_apellido' => '',
            'correo' => 'no-es-correo',
            'contrasena' => 'corta',
            'contrasena_confirmation' => 'otra',
        ])->assertSessionHasErrors(['primer_nombre', 'primer_apellido', 'correo', 'contrasena']);
    }

    public function test_exige_correo_y_contrasena_al_iniciar_sesion(): void
    {
        $this->from('/iniciar-sesion')->post('/iniciar-sesion', [])
            ->assertSessionHasErrors(['correo', 'contrasena']);
    }

    public function test_alias_login_y_register_muestran_formularios(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_usuario_autenticado_no_ve_formularios_de_invitado(): void
    {
        $this->registrarUsuario();

        $this->get('/registro')->assertRedirect(route('dashboard'));
        $this->get('/iniciar-sesion')->assertRedirect(route('dashboard'));
    }

    public function test_invitado_no_puede_cerrar_sesion(): void
    {
        $this->post('/cerrar-sesion')->assertRedirect(route('iniciar-sesion'));
    }

    public function test_limita_intentos_de_inicio_de_sesion(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/iniciar-sesion', [
                'correo' => 'ana@olimpia.com',
                'contrasena' => 'password1',
            ]);
        }

        $this->post('/iniciar-sesion', [
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
        ])->assertStatus(429);
    }

    private function registrarUsuario(string $correo = 'ana@olimpia.com'): void
    {
        $this->post('/registro', [
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => $correo,
            'contrasena' => 'password1',
            'contrasena_confirmation' => 'password1',
        ])->assertRedirect(route('dashboard'));
    }
}
