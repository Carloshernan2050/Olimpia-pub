<?php

namespace Tests\Feature\Repositories;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\ContenidoInicioRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Repositories\PromocionRepositoryInterface;
use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\DTOs\Dashboard\FiltroInventarioDatos;
use App\Enums\EstadoStockInventario;
use App\Enums\PosicionInicio;
use App\Enums\TipoBloqueInicio;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoriosEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
    }

    public function test_repositorio_de_roles_crea_busca_y_lista(): void
    {
        $roles = $this->app->make(RolRepositoryInterface::class);

        $this->assertNotNull($roles->findByNombre('cliente'));
        $this->assertNull($roles->findByNombre('inexistente'));
        $this->assertGreaterThanOrEqual(4, $roles->all()->count());

        $nuevo = $roles->create(['nombre_rol' => 'auditor']);
        $this->assertSame('auditor', $nuevo->nombre_rol);
        $this->assertTrue($nuevo->usuarios()->doesntExist());
    }

    public function test_repositorio_de_usuarios_crea_busca_y_lista(): void
    {
        $roles = $this->app->make(RolRepositoryInterface::class);
        $usuarios = $this->app->make(UsuarioRepositoryInterface::class);
        $rol = $roles->findByNombre('cliente');

        $this->assertNull($usuarios->findByCorreo('ana@olimpia.com'));

        $usuario = $usuarios->create([
            'primer_nombre' => 'Ana',
            'segundo_nombre' => null,
            'primer_apellido' => 'Perez',
            'segundo_apellido' => null,
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'estado' => 'activo',
            'id_rol' => $rol->id_rol,
        ]);

        $this->assertSame('id_usuario', $usuario->getAuthIdentifierName());
        $this->assertSame('contrasena', $usuario->getAuthPasswordName());
        $this->assertNotSame('password1', $usuario->getAuthPassword());
        $this->assertSame('ana@olimpia.com', $usuarios->findByCorreo('ana@olimpia.com')?->correo);
        $this->assertCount(1, $usuarios->all());
        $this->assertTrue($usuario->rol()->is($rol));
    }

    public function test_repositorios_de_catalogo_crean_y_buscan(): void
    {
        $categorias = $this->app->make(CategoriaRepositoryInterface::class);
        $productos = $this->app->make(ProductoRepositoryInterface::class);
        $codigos = $this->app->make(CodigoQrRepositoryInterface::class);
        $mesas = $this->app->make(MesaRepositoryInterface::class);

        $categoria = $categorias->create([
            'nombre' => 'Bebidas',
            'descripcion' => 'Refrescos',
        ]);
        $this->assertSame('Bebidas', $categorias->findByNombre('Bebidas')?->nombre);
        $this->assertNull($categorias->findByNombre('Snacks'));

        $producto = $productos->create([
            'nombre' => 'Cola',
            'descripcion' => '350 ml',
            'precio' => 3.5,
            'stock' => 10,
            'estado' => 'activo',
            'id_categoria' => $categoria->id_categoria,
        ]);
        $this->assertSame('Cola', $productos->findByNombre('Cola')?->nombre);
        $this->assertTrue($producto->categoria()->is($categoria));
        $this->assertTrue($categoria->productos()->whereKey($producto->id_producto)->exists());

        $codigo = $codigos->create([
            'numero_qr' => 1,
            'estado' => 'activo',
            'codigo_qr' => 'QR-001',
        ]);
        $this->assertSame(1, $codigos->findByNumero(1)?->numero_qr);

        $mesa = $mesas->create([
            'numero_mesa' => 4,
            'estado' => 'disponible',
            'id_qr' => $codigo->id_qr,
        ]);
        $this->assertSame(4, $mesas->findByNumero(4)?->numero_mesa);
        $this->assertTrue($mesa->codigoQr()->is($codigo));
        $this->assertTrue($codigo->mesa()->is($mesa));

        $agotado = $productos->create([
            'nombre' => 'Agua',
            'descripcion' => 'Sin stock',
            'precio' => 2,
            'stock' => 0,
            'estado' => 'activo',
            'id_categoria' => $categoria->id_categoria,
        ]);
        $productos->update($agotado, ['stock' => 1]);
        $this->assertSame(1, $productos->findById($agotado->id_producto)?->stock);

        $filtrados = $productos->filtrar(new FiltroInventarioDatos('Col', null, null, 1));
        $this->assertTrue($filtrados->contains('nombre', 'Cola'));
        $this->assertFalse($filtrados->contains('nombre', 'Agua'));

        $bajos = $productos->filtrar(new FiltroInventarioDatos(null, null, EstadoStockInventario::Bajo, 1));
        $this->assertTrue($bajos->contains('nombre', 'Agua'));
        $this->assertGreaterThanOrEqual(2, $productos->todos()->count());
        $this->assertCount(1, $categorias->todas());

        $resumen = $productos->resumenStock(10);
        $this->assertSame(2, $resumen['productos']);
        $this->assertSame(2, $resumen['bajo']);
    }

    public function test_repositorio_de_contenido_inicio_omite_inactivos(): void
    {
        $contenidos = $this->app->make(ContenidoInicioRepositoryInterface::class);

        $contenidos->create([
            'posicion' => PosicionInicio::SuperiorIzquierda->value,
            'tipo' => TipoBloqueInicio::Texto->value,
            'titulo' => 'Activo',
            'cuerpo' => 'Texto',
            'url_media' => null,
            'orden' => 1,
            'estado' => 'activo',
        ]);
        $contenidos->create([
            'posicion' => PosicionInicio::SuperiorDerecha->value,
            'tipo' => TipoBloqueInicio::Texto->value,
            'titulo' => 'Inactivo',
            'cuerpo' => 'Oculto',
            'url_media' => null,
            'orden' => 2,
            'estado' => 'inactivo',
        ]);

        $activos = $contenidos->activosPorPosicion();

        $this->assertTrue($activos->has(PosicionInicio::SuperiorIzquierda->value));
        $this->assertFalse($activos->has(PosicionInicio::SuperiorDerecha->value));
    }

    public function test_repositorio_de_contenido_inicio_crea_y_lista_activos(): void
    {
        $contenidos = $this->app->make(ContenidoInicioRepositoryInterface::class);

        $this->assertNull($contenidos->findByPosicion(PosicionInicio::SuperiorIzquierda->value));

        $bloque = $contenidos->create([
            'posicion' => PosicionInicio::SuperiorIzquierda->value,
            'tipo' => TipoBloqueInicio::Texto->value,
            'titulo' => 'Portada',
            'cuerpo' => 'Texto',
            'url_media' => null,
            'orden' => 1,
            'estado' => 'activo',
        ]);

        $this->assertSame('Portada', $contenidos->findByPosicion(PosicionInicio::SuperiorIzquierda->value)?->titulo);
        $this->assertTrue($contenidos->activosPorPosicion()->has(PosicionInicio::SuperiorIzquierda->value));
        $this->assertSame(PosicionInicio::SuperiorIzquierda, $bloque->posicion);
    }

    public function test_repositorio_de_promociones_lista_solo_vigentes(): void
    {
        $roles = $this->app->make(RolRepositoryInterface::class);
        $usuarios = $this->app->make(UsuarioRepositoryInterface::class);
        $promociones = $this->app->make(PromocionRepositoryInterface::class);
        $rol = $roles->findByNombre('cliente');

        $usuario = $usuarios->create([
            'primer_nombre' => 'Ana',
            'segundo_nombre' => null,
            'primer_apellido' => 'Perez',
            'segundo_apellido' => null,
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'estado' => 'activo',
            'id_rol' => $rol->id_rol,
        ]);

        $vigente = $promociones->create([
            'nombre' => 'Vigente',
            'descripcion' => 'Hoy',
            'descuento' => 15,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addWeek()->toDateString(),
            'estado' => 'activa',
            'id_usuario' => $usuario->id_usuario,
        ]);
        $promociones->create([
            'nombre' => 'Vencida',
            'descuento' => 10,
            'fecha_inicio' => now()->subWeek()->toDateString(),
            'fecha_fin' => now()->subDay()->toDateString(),
            'estado' => 'activa',
            'id_usuario' => $usuario->id_usuario,
        ]);

        $activas = $promociones->activas();

        $this->assertSame('Vigente', $vigente->nombre);
        $this->assertCount(1, $activas);
        $this->assertTrue($activas->contains('nombre', 'Vigente'));
        $this->assertFalse($activas->contains('nombre', 'Vencida'));

        $alfa = $promociones->create([
            'nombre' => 'Alfa',
            'descuento' => 5,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addWeek()->toDateString(),
            'estado' => 'activa',
            'id_usuario' => $usuario->id_usuario,
        ]);
        $promociones->update($alfa, ['nombre' => 'Alfa plus']);
        $this->assertSame('Alfa plus', $promociones->findById($alfa->id_promocion)?->nombre);

        $ordenadas = $promociones->activas();
        $this->assertSame(['Alfa plus', 'Vigente'], $ordenadas->pluck('nombre')->all());

        $this->assertGreaterThanOrEqual(2, $promociones->todas()->count());

        $promociones->delete($alfa);
        $this->assertNull($promociones->findById($alfa->id_promocion));
    }

    public function test_repositorio_de_movimientos_crea_lista_y_elimina(): void
    {
        $categorias = $this->app->make(CategoriaRepositoryInterface::class);
        $productos = $this->app->make(ProductoRepositoryInterface::class);
        $movimientos = $this->app->make(MovimientoInventarioRepositoryInterface::class);
        $usuarios = $this->app->make(UsuarioRepositoryInterface::class);
        $roles = $this->app->make(RolRepositoryInterface::class);
        $rol = $roles->findByNombre('cliente');

        $usuario = $usuarios->create([
            'primer_nombre' => 'Ana',
            'segundo_nombre' => null,
            'primer_apellido' => 'Perez',
            'segundo_apellido' => null,
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'estado' => 'activo',
            'id_rol' => $rol->id_rol,
        ]);
        $categoria = $categorias->create(['nombre' => 'Bebidas']);
        $producto = $productos->create([
            'nombre' => 'Cola',
            'precio' => 3.5,
            'stock' => 10,
            'estado' => 'activo',
            'id_categoria' => $categoria->id_categoria,
        ]);

        $movimiento = $movimientos->create([
            'tipo_movimiento' => 'entrada',
            'cantidad' => 4,
            'fecha' => now(),
            'id_producto' => $producto->id_producto,
            'id_usuario' => $usuario->id_usuario,
        ]);

        $this->assertSame(4, $movimientos->findById($movimiento->id_movimiento)?->cantidad);
        $this->assertSame(1, $movimientos->contar());
        $this->assertCount(1, $movimientos->recientes());
        $this->assertCount(1, $movimientos->porProducto($producto->id_producto));

        $movimientos->update($movimiento, ['cantidad' => 6]);
        $this->assertSame(6, $movimientos->findById($movimiento->id_movimiento)?->cantidad);

        $movimientos->eliminarDeProducto($producto->id_producto);
        $this->assertSame(0, $movimientos->contar());
    }
}
