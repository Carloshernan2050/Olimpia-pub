<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Dashboard\CatalogoMesasDatos;
use App\DTOs\Dashboard\FiltroMesasDatos;
use App\DTOs\Dashboard\GrupoMesaDatos;
use App\DTOs\Dashboard\LineaPedidoMesaDatos;
use App\DTOs\Dashboard\MesaTarjetaDatos;
use App\DTOs\Dashboard\RegistrarHistorialDatos;
use App\DTOs\Dashboard\TipoMesaDatos;
use App\Enums\AccionHistorial;
use App\Enums\EstadoPedido;
use App\Enums\TipoMesa;
use App\Models\CodigoQr;
use App\Models\DetallePedido;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use Tests\TestCase;

class MesasDatosTest extends TestCase
{
    public function test_filtro_predeterminado_no_esta_activo(): void
    {
        $filtro = FiltroMesasDatos::predeterminado();

        $this->assertFalse($filtro->estaActivo());
        $this->assertNull($filtro->tipo);
        $this->assertSame([], $filtro->query());
    }

    public function test_filtro_ignora_valores_invalidos(): void
    {
        $filtro = FiltroMesasDatos::fromInput('salon');

        $this->assertNull($filtro->tipo);
        $this->assertFalse($filtro->estaActivo());
    }

    public function test_filtro_conserva_tipo_en_enlaces(): void
    {
        $filtro = FiltroMesasDatos::fromInput('barra');

        $this->assertSame(TipoMesa::Barra, $filtro->tipo);
        $this->assertTrue($filtro->estaActivo());
        $this->assertSame(['tipo' => 'barra'], $filtro->query());
        $this->assertSame(['tipo' => 'grupo'], $filtro->queryConTipo(TipoMesa::Grupo));
        $this->assertSame(['tipo' => 'pedidos-activos'], $filtro->queryConTipo(TipoMesa::PedidosActivos));
        $this->assertSame([], $filtro->queryConTipo(null));
    }

    public function test_tipos_del_catalogo_incluyen_iconos(): void
    {
        $tipos = TipoMesaDatos::catalogo();

        $this->assertCount(4, $tipos);
        $this->assertSame('taburete', $tipos[0]->icono);
        $this->assertSame('Barra', $tipos[0]->etiqueta);
        $this->assertSame('mesa', $tipos[1]->icono);
        $this->assertSame('Mesa', $tipos[1]->etiqueta);
        $this->assertSame('grupo', $tipos[2]->icono);
        $this->assertSame('Grupo', $tipos[2]->etiqueta);
        $this->assertSame('carrito', $tipos[3]->icono);
        $this->assertSame('Pedidos activos', $tipos[3]->etiqueta);
        $this->assertSame('is-grupo', TipoMesa::Grupo->clase());
        $this->assertSame('is-pedidos-activos', TipoMesa::PedidosActivos->clase());
        $this->assertTrue(TipoMesa::PedidosActivos->esPedidosActivos());
        $this->assertFalse(TipoMesa::PedidosActivos->esPersistible());
        $this->assertSame('Todos', $tipos[0]->etiquetaFiltro(true));
        $this->assertSame('Barra', $tipos[0]->etiquetaFiltro(false));
        $this->assertCount(2, TipoMesaDatos::persistibles());
    }

    public function test_tarjeta_copia_codigo_qr_y_pedido_activo(): void
    {
        $tarjeta = MesaTarjetaDatos::fromModel($this->mesaOcupada(), '<svg></svg>');

        $this->assertSame(2, $tarjeta->id);
        $this->assertSame(2, $tarjeta->numero);
        $this->assertSame(TipoMesa::Mesa, $tarjeta->tipo);
        $this->assertSame('OLIMPIA-MESA-02', $tarjeta->codigo);
        $this->assertTrue($tarjeta->ocupada);
        $this->assertSame('Mesa 2', $tarjeta->etiqueta());
        $this->assertSame(['Limonada'], $tarjeta->lineasDePedido());
        $this->assertSame('Limonada', $tarjeta->resumenPedido());
        $this->assertSame('$ 17,00', $tarjeta->cuentaFormateada());
        $this->assertSame('$ 17,00', $tarjeta->pedidoActivo?->totalFormateado());
        $this->assertSame('$ 8,50', $tarjeta->pedidoActivo?->enOrden()[0]->precioFormateado());
    }

    public function test_catalogo_vacio_no_tiene_mesas(): void
    {
        $catalogo = new CatalogoMesasDatos([], TipoMesaDatos::catalogo());

        $this->assertFalse($catalogo->tieneMesas());
        $this->assertSame([], $catalogo->enOrden());
        $this->assertFalse($catalogo->puedeUnir());
        $this->assertFalse($catalogo->puedeLiberar());
        $this->assertSame(1, $catalogo->siguienteNumero);
    }

    public function test_etiqueta_del_grupo_une_los_numeros(): void
    {
        $this->assertSame('Mesas 1 y 2', GrupoMesaDatos::etiquetaDeNumeros([1, 2]));
        $this->assertSame('Mesas 1, 2 y 3', GrupoMesaDatos::etiquetaDeNumeros([1, 2, 3]));
    }

    public function test_historial_de_terminar_pedido_arma_la_descripcion(): void
    {
        $this->travelTo(now()->setTime(12, 0));
        $datos = RegistrarHistorialDatos::terminarPedido(4, 3, '$ 17,00');

        $this->assertSame(AccionHistorial::TerminarPedido, $datos->accion);
        $this->assertSame('Terminar pedido', $datos->accion->etiqueta());
        $this->assertSame('terminar_pedido', $datos->paraCrear()['accion']);
        $this->assertSame('Pedido de Mesa 3 cerrado. Total $ 17,00.', $datos->paraCrear()['descripcion']);
        $this->assertSame(4, $datos->paraCrear()['id_usuario']);
        $this->assertTrue($datos->paraCrear()['fecha']->equalTo(now()));
        $this->assertSame(route('mesas.pedido.terminar', 2), MesaTarjetaDatos::fromModel($this->mesaOcupada())->rutaTerminarPedido());
    }

    public function test_linea_sin_imagen_no_expone_url(): void
    {
        $linea = new LineaPedidoMesaDatos('Cola', 1, '3.50');

        $this->assertFalse($linea->tieneImagen());
        $this->assertNull($linea->urlImagenPublica());
    }

    private function mesaOcupada(): Mesa
    {
        $producto = new Producto(['nombre' => 'Limonada', 'precio' => '8.50']);
        $detalle = new DetallePedido([
            'cantidad' => 2,
            'precio_unitario' => '8.50',
            'subtotal' => '17.00',
        ]);
        $detalle->setRelation('producto', $producto);

        $pedido = new Pedido([
            'estado' => EstadoPedido::Activo,
            'total' => '17.00',
        ]);
        $pedido->id_pedido = 9;
        $pedido->setRelation('detalles', collect([$detalle]));

        $mesa = new Mesa([
            'numero_mesa' => 2,
            'tipo' => TipoMesa::Mesa,
        ]);
        $mesa->id_mesa = 2;
        $mesa->setRelation('codigoQr', new CodigoQr(['codigo_qr' => 'OLIMPIA-MESA-02']));
        $mesa->setRelation('pedidoActivo', $pedido);

        return $mesa;
    }
}
