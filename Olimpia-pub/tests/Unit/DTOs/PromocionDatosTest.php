<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Dashboard\CatalogoPromocionesDatos;
use App\DTOs\Dashboard\FiltroPromocionesDatos;
use App\DTOs\Dashboard\GuardarPromocionDatos;
use App\DTOs\Dashboard\PromocionGestionDatos;
use App\DTOs\Dashboard\PromocionTarjetaDatos;
use App\Models\Promocion;
use Tests\TestCase;

class PromocionDatosTest extends TestCase
{
    public function test_from_model_copia_nombre_descripcion_y_descuento(): void
    {
        $promocion = new Promocion([
            'nombre' => 'Combo hincha',
            'descripcion' => 'Cerveza y picada',
            'descuento' => '15.50',
        ]);
        $promocion->id_promocion = 3;

        $tarjeta = PromocionTarjetaDatos::fromModel($promocion);

        $this->assertSame(3, $tarjeta->id);
        $this->assertSame('Combo hincha', $tarjeta->nombre);
        $this->assertSame('Cerveza y picada', $tarjeta->detalle());
        $this->assertFalse($tarjeta->tieneImagen());
        $this->assertNull($tarjeta->urlImagenPublica());
    }

    public function test_sin_descripcion_usa_el_descuento(): void
    {
        $tarjeta = new PromocionTarjetaDatos(1, '2x1', null, '10.00');

        $this->assertSame('10% de descuento', $tarjeta->detalle());
    }

    public function test_url_absoluta_se_deja_igual(): void
    {
        $tarjeta = new PromocionTarjetaDatos(
            1,
            'Combo',
            'Detalle',
            '5',
            'https://olimpia.test/combo.jpg',
        );

        $this->assertTrue($tarjeta->tieneImagen());
        $this->assertSame('https://olimpia.test/combo.jpg', $tarjeta->urlImagenPublica());
    }

    public function test_url_local_pasa_por_asset(): void
    {
        $tarjeta = new PromocionTarjetaDatos(1, 'Combo', null, '5', '/media/promos/combo.jpg');

        $this->assertSame(asset('/media/promos/combo.jpg'), $tarjeta->urlImagenPublica());
    }

    public function test_ruta_en_disco_publico_usa_storage(): void
    {
        $tarjeta = new PromocionTarjetaDatos(1, 'Combo', null, '5', 'promociones/combo.jpg');

        $this->assertSame(asset('storage/promociones/combo.jpg'), $tarjeta->urlImagenPublica());
    }

    public function test_gestion_copia_periodo_e_imagen_del_modelo(): void
    {
        $promocion = new Promocion([
            'nombre' => 'Combo hincha',
            'descripcion' => 'Cerveza y picada',
            'descuento' => '15.50',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-08-31',
            'estado' => 'activa',
            'url_imagen' => 'promociones/combo.jpg',
        ]);
        $promocion->id_promocion = 3;

        $gestion = PromocionGestionDatos::fromModel($promocion);

        $this->assertSame(3, $gestion->id);
        $this->assertSame('2026-08-01', $gestion->fechaInicio);
        $this->assertSame('2026-08-31', $gestion->fechaFin);
        $this->assertTrue($gestion->estaActiva());
        $this->assertTrue($gestion->tieneImagen());
        $this->assertSame(asset('storage/promociones/combo.jpg'), $gestion->urlImagenPublica());
    }

    public function test_catalogo_vacio_no_tiene_promociones(): void
    {
        $catalogo = new CatalogoPromocionesDatos([]);

        $this->assertFalse($catalogo->tienePromociones());
        $this->assertSame([], $catalogo->enOrden());
    }

    public function test_catalogo_devuelve_las_tarjetas_en_el_mismo_orden(): void
    {
        $primera = new PromocionTarjetaDatos(1, 'A', null, '5');
        $catalogo = new CatalogoPromocionesDatos([$primera]);

        $this->assertTrue($catalogo->tienePromociones());
        $this->assertSame([$primera], $catalogo->enOrden());
    }

    public function test_filtro_predeterminado_no_esta_activo(): void
    {
        $filtro = FiltroPromocionesDatos::predeterminado();

        $this->assertFalse($filtro->estaActivo());
        $this->assertNull($filtro->desde);
        $this->assertNull($filtro->hasta);
        $this->assertSame([], $filtro->query());
    }

    public function test_filtro_ignora_fechas_invalidas(): void
    {
        $filtro = FiltroPromocionesDatos::fromInput('ayer', '2026-08-01');

        $this->assertNull($filtro->desde);
        $this->assertSame('2026-08-01', $filtro->hasta);
        $this->assertTrue($filtro->estaActivo());
    }

    public function test_guardar_promocion_normaliza_descripcion_y_estado(): void
    {
        $datos = GuardarPromocionDatos::fromValidated([
            'nombre' => '  Combo  ',
            'descripcion' => '   ',
            'descuento' => '12.5',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-08-10',
            'estado' => 'inactiva',
        ]);

        $this->assertSame('Combo', $datos->nombre);
        $this->assertNull($datos->descripcion);
        $this->assertSame('inactiva', $datos->estado);
        $this->assertSame('2026-08-01', $datos->periodo->inicio);
        $this->assertSame('2026-08-10', $datos->periodo->fin);
        $this->assertSame(7, $datos->paraCrear(7)['id_usuario']);
        $this->assertArrayNotHasKey('id_usuario', $datos->paraActualizar());
    }
}
