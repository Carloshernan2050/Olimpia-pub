<?php

namespace Tests\Feature;

use App\Enums\EstadoEvento;
use App\Models\Evento;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
    }

    public function test_el_megafono_abre_el_catalogo(): void
    {
        $this->autenticar();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('eventos').'"', false)
            ->assertSee('aria-label="Eventos"', false);
    }

    public function test_el_invitado_no_puede_ver_eventos(): void
    {
        $this->get(route('eventos'))->assertRedirect(route('iniciar-sesion'));
        $this->get(route('eventos.detalle', 1))->assertRedirect(route('iniciar-sesion'));
        $this->put(route('eventos.actualizar', 1), $this->datosEvento())->assertRedirect(route('iniciar-sesion'));
        $this->delete(route('eventos.eliminar', 1))->assertRedirect(route('iniciar-sesion'));
    }

    public function test_sin_eventos_muestra_el_encabezado_y_ninguna_tarjeta(): void
    {
        $this->autenticar();

        $this->get(route('eventos'))
            ->assertOk()
            ->assertSee('Eventos —', false)
            ->assertSee('id="titulo-eventos"', false)
            ->assertSee('Filtrar')
            ->assertSee('Desde')
            ->assertSee('Hasta')
            ->assertSee('aria-current="page"', false)
            ->assertSee('aria-label="Agregar evento"', false)
            ->assertSee('Agregar evento')
            ->assertSee('No hay eventos disponibles')
            ->assertDontSee('grilla-eventos', false)
            ->assertDontSee('evento-tarjeta', false)
            ->assertDontSee('Ver Detalles');
    }

    public function test_con_evento_muestra_la_tarjeta(): void
    {
        $this->autenticar();
        $this->crearEvento([
            'nombre' => 'Noche de karaoke',
            'descripcion' => 'Micrófono abierto',
        ]);

        $this->get(route('eventos'))
            ->assertOk()
            ->assertSee('grilla-eventos', false)
            ->assertSee('Noche de karaoke')
            ->assertSee('Micrófono abierto')
            ->assertSee('Ver Detalles');
    }

    public function test_filtra_por_rango_de_fechas(): void
    {
        $this->autenticar();
        $this->crearEvento([
            'nombre' => 'Historico',
            'fecha' => now()->subWeek()->toDateString(),
        ]);
        $this->crearEvento([
            'nombre' => 'Proximo',
            'fecha' => now()->addDays(3)->toDateString(),
        ]);

        $sinFiltro = $this->get(route('eventos'))
            ->assertOk()
            ->assertSee('Historico')
            ->assertSee('Proximo')
            ->getContent();

        $this->assertSame(2, substr_count($sinFiltro, 'class="evento-tarjeta"'));

        $conFechas = $this->get(route('eventos', [
            'desde' => now()->addDay()->toDateString(),
            'hasta' => now()->addWeek()->toDateString(),
        ]))
            ->assertOk()
            ->getContent();

        $this->assertSame(1, substr_count($conFechas, 'class="evento-tarjeta"'));
        $this->assertStringContainsString('Proximo', $conFechas);
    }

    public function test_ver_detalles_abre_el_modal_centrado(): void
    {
        $this->autenticar();
        $evento = $this->crearEvento([
            'nombre' => 'Cata de cervezas',
            'descripcion' => 'Seis estilos',
            'hora' => '19:00',
        ]);

        $this->get(route('eventos', ['ver' => $evento->id_evento]))
            ->assertOk()
            ->assertSee('data-modal-evento-detalle', false)
            ->assertSee('Cata de cervezas')
            ->assertSee('Seis estilos')
            ->assertSee('19:00')
            ->assertSee('Programado')
            ->assertSee('Cerrar');
    }

    public function test_guarda_la_imagen_y_la_muestra_en_la_tarjeta(): void
    {
        Storage::fake('public');
        $this->autenticar();

        $this->post(route('eventos.guardar'), $this->datosEvento([
            'nombre' => 'Noche con foto',
            'imagen' => UploadedFile::fake()->image('noche.jpg', 400, 300),
        ]))
            ->assertRedirect(route('eventos'));

        $evento = Evento::query()->where('nombre', 'Noche con foto')->first();
        $this->assertNotNull($evento?->url_imagen);
        Storage::disk('public')->assertExists($evento->url_imagen);

        $this->get(route('eventos'))
            ->assertOk()
            ->assertSee('storage/'.$evento->url_imagen, false)
            ->assertSee('alt="Noche con foto"', false);
    }

    public function test_el_invitado_no_puede_crear_eventos(): void
    {
        $this->post(route('eventos.guardar'), $this->datosEvento())
            ->assertRedirect(route('iniciar-sesion'));
    }

    public function test_crea_un_evento_y_lo_muestra_en_el_catalogo(): void
    {
        $this->autenticar();

        $this->post(route('eventos.guardar'), $this->datosEvento([
            'nombre' => 'Noche de karaoke',
            'descripcion' => 'Micrófono abierto',
        ]))
            ->assertRedirect(route('eventos'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('evento', [
            'nombre' => 'Noche de karaoke',
            'descripcion' => 'Micrófono abierto',
        ]);

        $this->get(route('eventos'))
            ->assertOk()
            ->assertSee('Noche de karaoke')
            ->assertSee('Micrófono abierto')
            ->assertSee('Ver Detalles')
            ->assertSee('Evento creado correctamente.')
            ->assertSee('data-aviso', false);
    }

    public function test_validacion_reabre_el_modal_sin_guardar(): void
    {
        $this->autenticar();

        $this->from(route('eventos'))
            ->followingRedirects()
            ->post(route('eventos.guardar'), [
                'nombre' => '',
                'fecha' => '',
                'hora' => '',
            ])
            ->assertOk()
            ->assertSee('El nombre es obligatorio.')
            ->assertSee('data-abrir', false);

        $this->assertDatabaseCount('evento', 0);
    }

    public function test_actualiza_y_elimina_un_evento(): void
    {
        $this->autenticar();
        $evento = $this->crearEvento(['nombre' => 'Original']);

        $this->put(route('eventos.actualizar', $evento->id_evento), $this->datosEvento([
            'nombre' => 'Actualizado',
        ]))
            ->assertRedirect(route('eventos'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('evento', ['nombre' => 'Actualizado']);

        $this->delete(route('eventos.eliminar', $evento->id_evento))
            ->assertRedirect(route('eventos'));

        $this->assertDatabaseMissing('evento', ['id_evento' => $evento->id_evento]);
    }

    public function test_abre_el_formulario_de_edicion(): void
    {
        $this->autenticar();
        $evento = $this->crearEvento(['nombre' => 'Editable']);

        $this->get(route('eventos', ['editar' => $evento->id_evento]))
            ->assertOk()
            ->assertSee('Editar evento')
            ->assertSee('value="Editable"', false)
            ->assertSee('data-abrir', false);
    }

    public function test_evento_inexistente_vuelve_al_catalogo(): void
    {
        $this->autenticar();

        $this->get(route('eventos', ['ver' => 999]))
            ->assertRedirect(route('eventos'))
            ->assertSessionHas('error', 'El evento no existe.');
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function datosEvento(array $extra = []): array
    {
        return [
            'nombre' => 'Evento',
            'descripcion' => 'Detalle',
            'fecha' => now()->toDateString(),
            'hora' => '20:00',
            'estado' => EstadoEvento::Programado->value,
            ...$extra,
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function crearEvento(array $extra = []): Evento
    {
        return Evento::query()->create([
            ...$this->datosEvento($extra),
            'id_usuario' => auth()->id(),
        ]);
    }
}
