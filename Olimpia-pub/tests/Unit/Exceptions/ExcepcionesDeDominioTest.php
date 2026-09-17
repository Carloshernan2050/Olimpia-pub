<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\Autenticacion\CorreoYaRegistradoException;
use App\Exceptions\Autenticacion\CredencialesInvalidasException;
use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Exceptions\Autenticacion\UsuarioInactivoException;
use App\Exceptions\BaseDatos\ArchivoSqliteNoCreadoException;
use App\Exceptions\BaseDatos\BaseDatosNoCreadaException;
use App\Exceptions\BaseDatos\ConexionNoEncontradaException;
use App\Exceptions\BaseDatos\DirectorioSqliteNoCreadoException;
use App\Exceptions\BaseDatos\DriverNoSoportadoException;
use App\Exceptions\Evento\EventoNoEncontradoException;
use App\Exceptions\Inventario\AccesoInventarioDenegadoException;
use App\Exceptions\Inventario\MovimientoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoConPedidosException;
use App\Exceptions\Inventario\ProductoInventarioNoEncontradoException;
use App\Exceptions\Inventario\StockInsuficienteException;
use App\Exceptions\Mesa\GrupoConVariosPedidosException;
use App\Exceptions\Mesa\GrupoInsuficienteException;
use App\Exceptions\Mesa\GrupoNoEncontradoException;
use App\Exceptions\Mesa\MesaConPedidosException;
use App\Exceptions\Mesa\MesaEnGrupoException;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Exceptions\Mesa\MesaNoUnibleException;
use App\Exceptions\Mesa\MesaNumeroDuplicadoException;
use App\Exceptions\Mesa\MesaYaAgrupadaException;
use App\Exceptions\Mesa\PedidoActivoNoEncontradoException;
use App\Exceptions\Mesa\PedidoActivoYaExisteException;
use App\Exceptions\Promocion\PromocionNoEncontradaException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ExcepcionesDeDominioTest extends TestCase
{
    public function test_excepciones_de_autenticacion_exponen_mensaje_por_defecto(): void
    {
        $this->assertNotSame('', (new CorreoYaRegistradoException)->getMessage());
        $this->assertNotSame('', (new CredencialesInvalidasException)->getMessage());
        $this->assertNotSame('', (new UsuarioInactivoException)->getMessage());
        $this->assertSame(
            'El rol requerido no está configurado.',
            (new RolNoConfiguradoException)->getMessage()
        );
        $this->assertSame('Falta el rol.', (new RolNoConfiguradoException('Falta el rol.'))->getMessage());
        $this->assertSame('La promoción no existe.', (new PromocionNoEncontradaException)->getMessage());
        $this->assertSame('El evento no existe.', (new EventoNoEncontradoException)->getMessage());
        $this->assertSame('El producto no existe en el inventario.', (new ProductoInventarioNoEncontradoException)->getMessage());
        $this->assertSame('El movimiento de inventario no existe.', (new MovimientoInventarioNoEncontradoException)->getMessage());
        $this->assertSame('No hay stock suficiente para registrar el movimiento.', (new StockInsuficienteException)->getMessage());
        $this->assertSame('No se puede eliminar el producto porque tiene pedidos asociados.', (new ProductoConPedidosException)->getMessage());
        $this->assertSame('No tienes permiso para acceder al inventario.', (new AccesoInventarioDenegadoException)->getMessage());
        $this->assertSame('La mesa no existe.', (new MesaNoEncontradaException)->getMessage());
        $this->assertSame('La mesa ya tiene un pedido activo.', (new PedidoActivoYaExisteException)->getMessage());
        $this->assertSame('La mesa no tiene un pedido activo para terminar.', (new PedidoActivoNoEncontradoException)->getMessage());
        $this->assertSame('Ya existe una mesa con ese número.', (new MesaNumeroDuplicadoException)->getMessage());
        $this->assertSame('No se puede eliminar la mesa porque tiene pedidos asociados.', (new MesaConPedidosException)->getMessage());
        $this->assertSame('El grupo de mesas no existe.', (new GrupoNoEncontradoException)->getMessage());
        $this->assertSame('Selecciona al menos dos mesas para unirlas.', (new GrupoInsuficienteException)->getMessage());
        $this->assertSame('Solo se pueden unir mesas. La barra no forma grupos.', (new MesaNoUnibleException)->getMessage());
        $this->assertSame('Hay una mesa que ya pertenece a otro grupo.', (new MesaYaAgrupadaException)->getMessage());
        $this->assertSame('El grupo solo puede tener un pedido activo.', (new GrupoConVariosPedidosException)->getMessage());
        $this->assertSame('Separa el grupo antes de eliminar o cambiar esa mesa.', (new MesaEnGrupoException)->getMessage());
        $this->assertSame(
            'El correo ya está registrado.',
            CorreoYaRegistradoException::mensajePorDefecto()
        );
    }

    public function test_excepciones_de_base_datos_incluyen_contexto(): void
    {
        $this->assertSame(
            'No existe la conexión de base de datos [mysql].',
            (new ConexionNoEncontradaException('mysql'))->getMessage()
        );
        $this->assertSame(
            'No se pudo crear el archivo SQLite: /tmp/db.sqlite',
            (new ArchivoSqliteNoCreadoException('/tmp/db.sqlite'))->getMessage()
        );
        $this->assertSame(
            'No se pudo crear el directorio de SQLite: /tmp/data',
            (new DirectorioSqliteNoCreadoException('/tmp/data'))->getMessage()
        );
        $this->assertNotSame('', (new DriverNoSoportadoException)->getMessage());
    }

    public function test_base_datos_no_creada_conserva_la_causa(): void
    {
        $causa = new RuntimeException('conexion rechazada');
        $excepcion = new BaseDatosNoCreadaException(previous: $causa);

        $this->assertSame($causa, $excepcion->getPrevious());
        $this->assertStringContainsString('MySQL', $excepcion->getMessage());
    }
}
