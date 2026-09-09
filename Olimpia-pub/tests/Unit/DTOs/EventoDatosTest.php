<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Dashboard\CatalogoEventosDatos;
use App\DTOs\Dashboard\EventoDetalleDatos;
use App\DTOs\Dashboard\EventoGestionDatos;
use App\DTOs\Dashboard\EventoTarjetaDatos;
use App\DTOs\Dashboard\FiltroRangoFechasDatos;
use App\DTOs\Dashboard\GuardarEventoDatos;
use App\Enums\EstadoEvento;
use App\Models\Evento;
use Tests\TestCase;

class EventoDatosTest extends TestCase
{
    public function test_from_model_copia_nombre_descripcion_fecha_y_hora(): void
    {
        $evento = new Evento([
            'nombre' => 'Noche de karaoke',
            'descripcion' => 'Micrófono abierto',
            'fecha' => '2026-09-10',
            'hora' => '21:00:00',
            'estado' => EstadoEvento::Programado,
        ]);
        $evento->id_evento = 4;

        $tarjeta = EventoTarjetaDatos::fromModel($evento);

        $this->assertSame(4, $tarjeta->id);
        $this->assertSame('Noche de karaoke', $tarjeta->nombre);
        $this->assertSame('Micrófono abierto', $tarjeta->detalle());
        $this->assertSame('10/09/2026', $tarjeta->fechaFormateada());
        $this->assertSame('21:00', $tarjeta->horaFormateada());
        $this->assertSame('programado', $tarjeta->estado);
        $this->assertFalse($tarjeta->tieneImagen());
    }

    public function test_sin_descripcion_usa_fecha_y_hora(): void
    {
        $tarjeta = new EventoTarjetaDatos(1, 'Trivia', null, '2026-09-12', '20:00', 'programado');

        $this->assertSame('12/09/2026 · 20:00', $tarjeta->detalle());
    }

    public function test_detalle_copia_la_etiqueta_del_estado(): void
    {
        $evento = new Evento([
            'nombre' => 'DJ en vivo',
            'descripcion' => null,
            'fecha' => '2026-09-15',
            'hora' => '22:00',
            'estado' => EstadoEvento::Cancelado,
        ]);
        $evento->id_evento = 8;

        $detalle = EventoDetalleDatos::fromModel($evento);

        $this->assertSame(8, $detalle->tarjeta->id);
        $this->assertSame('Cancelado', $detalle->estadoEtiqueta);
        $this->assertSame('15/09/2026', $detalle->tarjeta->fechaFormateada());
        $this->assertSame('22:00', $detalle->tarjeta->horaFormateada());
    }

    public function test_tarjeta_normaliza_el_estado_cuando_llega_como_texto(): void
    {
        $evento = new Evento([
            'nombre' => 'Trivia',
            'descripcion' => null,
            'fecha' => '2026-09-12',
            'hora' => '20:00',
        ]);
        $evento->id_evento = 2;
        $evento->mergeCasts(['estado' => 'string']);
        $evento->setAttribute('estado', 'finalizado');

        $tarjeta = EventoTarjetaDatos::fromModel($evento);

        $this->assertSame('finalizado', $tarjeta->estado);
    }

    public function test_catalogo_vacio_no_tiene_eventos(): void
    {
        $catalogo = new CatalogoEventosDatos([]);

        $this->assertFalse($catalogo->tieneEventos());
        $this->assertSame([], $catalogo->enOrden());
    }

    public function test_catalogo_devuelve_las_tarjetas_en_el_mismo_orden(): void
    {
        $primera = new EventoTarjetaDatos(1, 'A', null, '2026-09-10', '20:00', 'programado');
        $catalogo = new CatalogoEventosDatos([$primera]);

        $this->assertTrue($catalogo->tieneEventos());
        $this->assertSame([$primera], $catalogo->enOrden());
    }

    public function test_filtro_predeterminado_no_esta_activo(): void
    {
        $filtro = FiltroRangoFechasDatos::predeterminado();

        $this->assertFalse($filtro->estaActivo());
        $this->assertNull($filtro->desde);
        $this->assertNull($filtro->hasta);
        $this->assertSame([], $filtro->query());
    }

    public function test_filtro_ignora_fechas_invalidas(): void
    {
        $filtro = FiltroRangoFechasDatos::fromInput('ayer', '2026-09-01');

        $this->assertNull($filtro->desde);
        $this->assertSame('2026-09-01', $filtro->hasta);
        $this->assertTrue($filtro->estaActivo());
        $this->assertSame(['hasta' => '2026-09-01'], $filtro->query());
    }

    public function test_guardar_evento_normaliza_descripcion_hora_y_estado(): void
    {
        $datos = GuardarEventoDatos::fromValidated([
            'nombre' => '  Karaoke  ',
            'descripcion' => '   ',
            'fecha' => '2026-09-10',
            'hora' => '21:00:00',
            'estado' => 'otro',
        ]);

        $this->assertSame('Karaoke', $datos->nombre);
        $this->assertNull($datos->descripcion);
        $this->assertSame('21:00', $datos->hora);
        $this->assertSame('programado', $datos->estado);
        $this->assertSame(7, $datos->paraCrear(7)['id_usuario']);
        $this->assertArrayNotHasKey('id_usuario', $datos->paraActualizar());
    }

    public function test_gestion_copia_fecha_y_hora_del_modelo(): void
    {
        $evento = new Evento([
            'nombre' => 'DJ en vivo',
            'descripcion' => 'Sesión',
            'fecha' => '2026-09-15',
            'hora' => '22:00:00',
            'estado' => EstadoEvento::Programado,
        ]);
        $evento->id_evento = 5;

        $gestion = EventoGestionDatos::fromModel($evento);

        $this->assertSame(5, $gestion->id);
        $this->assertSame('2026-09-15', $gestion->fecha);
        $this->assertSame('22:00', $gestion->hora);
        $this->assertSame('2026-09-15 · 22:00', $gestion->cuando());
    }
}
