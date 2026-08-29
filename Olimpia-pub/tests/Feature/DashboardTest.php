<?php

namespace Tests\Feature;

use App\Models\ContenidoInicio;
use Database\Seeders\ContenidoInicioSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolSeeder::class,
            ContenidoInicioSeeder::class,
        ]);
    }

    public function test_el_invitado_no_puede_ver_el_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('iniciar-sesion'));
    }

    public function test_el_usuario_autenticado_ve_home_con_textos_y_video(): void
    {
        $this->autenticar();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('El partido se vive mejor en Olimpia')
            ->assertSee('Hoy en barra')
            ->assertSee('Carta de temporada')
            ->assertSee('/media/inicio/portada.mp4', false)
            ->assertSee('kind="subtitles"', false)
            ->assertSee('kind="descriptions"', false)
            ->assertSee('/media/inicio/portada.es.vtt', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_despues_del_login_redirige_al_dashboard(): void
    {
        $this->registrarUsuarioCliente('ana@olimpia.com')->assertRedirect(route('dashboard'));

        $this->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_el_icono_de_home_apunta_al_dashboard(): void
    {
        $this->autenticar();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertSee('aria-label="Inicio"', false);
    }

    public function test_muestra_imagenes_perfil_y_titulo_de_inicio(): void
    {
        $this->autenticar();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Inicio —', false)
            ->assertSee('Hola, Ana')
            ->assertSee('Cerrar sesión')
            ->assertSee('/media/inicio/zona-pantallas.svg', false)
            ->assertSee('/media/inicio/terraza.svg', false)
            ->assertSee('Próximamente', false);
    }

    public function test_home_sin_contenido_sigue_respondiendo(): void
    {
        ContenidoInicio::query()->delete();
        $this->autenticar();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('El partido se vive mejor en Olimpia')
            ->assertSee('kind="subtitles"', false)
            ->assertSee('kind="descriptions"', false)
            ->assertSee('media/inicio/portada.es.vtt', false);
    }

    public function test_el_seeder_no_duplica_bloques_existentes(): void
    {
        $this->seed(ContenidoInicioSeeder::class);

        $this->assertDatabaseCount('contenido_inicio', 6);
    }
}
