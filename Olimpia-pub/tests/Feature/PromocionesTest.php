<?php

namespace Tests\Feature;

use App\Models\Promocion;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromocionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
    }

    public function test_el_invitado_no_puede_ver_promociones(): void
    {
        $this->get(route('promociones'))->assertRedirect(route('iniciar-sesion'));
    }

    public function test_sin_promociones_muestra_el_encabezado_y_ninguna_tarjeta(): void
    {
        $this->autenticar();

        $this->get(route('promociones'))
            ->assertOk()
            ->assertSee('Promociones —', false)
            ->assertSee('id="titulo-promociones"', false)
            ->assertSee('Filtrar')
            ->assertSee('Desde')
            ->assertSee('Hasta')
            ->assertDontSee('Fecha de inicio (antigua primero)')
            ->assertSee('aria-label="Agregar promoción"', false)
            ->assertSee('Agregar promoción')
            ->assertSee('aria-current="page"', false)
            ->assertSee('No hay promociones disponibles')
            ->assertDontSee('grilla-promociones', false)
            ->assertDontSee('promocion-tarjeta', false)
            ->assertDontSee('Comprar');
    }

    public function test_el_invitado_no_puede_crear_promociones(): void
    {
        $this->post(route('promociones.guardar'), $this->datosPromocion())
            ->assertRedirect(route('iniciar-sesion'));
    }

    public function test_crea_una_promocion_y_la_muestra_en_el_catalogo(): void
    {
        $this->autenticar();

        $this->post(route('promociones.guardar'), $this->datosPromocion([
            'nombre' => 'Combo hincha',
            'descripcion' => 'Cerveza y picada',
        ]))
            ->assertRedirect(route('promociones'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('promocion', [
            'nombre' => 'Combo hincha',
            'descripcion' => 'Cerveza y picada',
        ]);

        $this->get(route('promociones'))
            ->assertOk()
            ->assertSee('Combo hincha')
            ->assertSee('Cerveza y picada')
            ->assertSee('Comprar')
            ->assertSee('Promoción creada correctamente.')
            ->assertSee('data-aviso', false);
    }

    public function test_validacion_reabre_el_modal_sin_guardar(): void
    {
        $this->autenticar();

        $this->from(route('promociones'))
            ->followingRedirects()
            ->post(route('promociones.guardar'), [
                'nombre' => '',
                'descuento' => '',
                'fecha_inicio' => '',
                'fecha_fin' => '',
            ])
            ->assertOk()
            ->assertSee('El nombre es obligatorio.')
            ->assertSee('data-abrir', false);

        $this->assertDatabaseCount('promocion', 0);
    }

    public function test_actualiza_y_elimina_una_promocion(): void
    {
        $this->autenticar();
        $promocion = $this->crearPromocion(['nombre' => 'Original']);

        $this->put(route('promociones.actualizar', $promocion->id_promocion), $this->datosPromocion([
            'nombre' => 'Actualizada',
        ]))
            ->assertRedirect(route('promociones'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('promocion', ['nombre' => 'Actualizada']);

        $this->delete(route('promociones.eliminar', $promocion->id_promocion))
            ->assertRedirect(route('promociones'));

        $this->assertDatabaseMissing('promocion', ['id_promocion' => $promocion->id_promocion]);
    }

    public function test_guarda_la_imagen_y_la_muestra_en_la_tarjeta(): void
    {
        Storage::fake('public');
        $this->autenticar();

        $this->post(route('promociones.guardar'), $this->datosPromocion([
            'nombre' => 'Combo con foto',
            'imagen' => UploadedFile::fake()->image('combo.jpg', 400, 280),
        ]))
            ->assertRedirect(route('promociones'));

        $promocion = Promocion::query()->where('nombre', 'Combo con foto')->first();
        $this->assertNotNull($promocion?->url_imagen);
        Storage::disk('public')->assertExists($promocion->url_imagen);

        $this->get(route('promociones'))
            ->assertOk()
            ->assertSee('storage/'.$promocion->url_imagen, false)
            ->assertSee('alt="Combo con foto"', false);
    }

    public function test_filtra_por_rango_de_fechas(): void
    {
        $this->autenticar();
        $this->crearPromocion([
            'nombre' => 'Historica',
            'fecha_inicio' => now()->subWeek()->toDateString(),
            'fecha_fin' => now()->subDay()->toDateString(),
        ]);
        $this->crearPromocion(['nombre' => 'Vigente hoy']);

        $sinFiltro = $this->get(route('promociones'))
            ->assertOk()
            ->assertSee('Vigente hoy')
            ->assertSee('Historica')
            ->getContent();

        $this->assertSame(1, substr_count($sinFiltro, 'class="promocion-tarjeta"'));
        $this->assertStringContainsString('Vigente hoy', $sinFiltro);

        $conFechas = $this->get(route('promociones', [
            'desde' => now()->subWeek()->toDateString(),
            'hasta' => now()->subDay()->toDateString(),
        ]))
            ->assertOk()
            ->getContent();

        $this->assertSame(1, substr_count($conFechas, 'class="promocion-tarjeta"'));
        $this->assertStringContainsString('Historica', $conFechas);
    }

    public function test_con_promocion_vigente_muestra_la_tarjeta(): void
    {
        $this->autenticar();
        $this->crearPromocion([
            'nombre' => 'Combo hincha',
            'descripcion' => 'Cerveza y picada',
        ]);

        $this->get(route('promociones'))
            ->assertOk()
            ->assertSee('grilla-promociones', false)
            ->assertSee('Combo hincha')
            ->assertSee('Cerveza y picada')
            ->assertSee('Comprar');
    }

    public function test_no_muestra_promociones_inactivas_ni_fuera_de_fecha(): void
    {
        $this->autenticar();
        $this->crearPromocion([
            'nombre' => 'Inactiva',
            'estado' => 'inactiva',
        ]);
        $this->crearPromocion([
            'nombre' => 'Vencida',
            'fecha_inicio' => now()->subWeek()->toDateString(),
            'fecha_fin' => now()->subDay()->toDateString(),
        ]);
        $this->crearPromocion([
            'nombre' => 'Futura',
            'fecha_inicio' => now()->addDay()->toDateString(),
            'fecha_fin' => now()->addWeek()->toDateString(),
        ]);

        $this->get(route('promociones'))
            ->assertOk()
            ->assertDontSee('class="promocion-tarjeta"', false)
            ->assertDontSee('Comprar')
            ->assertSee('Inactiva')
            ->assertSee('Vencida')
            ->assertSee('Futura');
    }

    public function test_abre_el_formulario_de_edicion(): void
    {
        $this->autenticar();
        $promocion = $this->crearPromocion(['nombre' => 'Editable']);

        $this->get(route('promociones', ['editar' => $promocion->id_promocion]))
            ->assertOk()
            ->assertSee('Editar promoción')
            ->assertSee('value="Editable"', false)
            ->assertSee('data-abrir', false);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function datosPromocion(array $extra = []): array
    {
        return [
            'nombre' => 'Promo',
            'descripcion' => 'Detalle',
            'descuento' => 10,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addWeek()->toDateString(),
            'estado' => 'activa',
            ...$extra,
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function crearPromocion(array $extra = []): Promocion
    {
        return Promocion::query()->create([
            ...$this->datosPromocion($extra),
            'id_usuario' => auth()->id(),
        ]);
    }
}
