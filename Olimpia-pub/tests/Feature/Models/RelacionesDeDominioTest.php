<?php

namespace Tests\Feature\Models;

use App\Models\Categoria;
use App\Models\CodigoQr;
use App\Models\DetallePedido;
use App\Models\Evento;
use App\Models\Historial;
use App\Models\Mesa;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Reporte;
use App\Models\Rol;
use App\Models\Usuario;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelacionesDeDominioTest extends TestCase
{
    use RefreshDatabase;

    public function test_relaciones_de_usuario_y_catalogo(): void
    {
        $this->seed(RolSeeder::class);

        $rol = Rol::query()->where('nombre_rol', 'cliente')->firstOrFail();
        $usuario = Usuario::query()->create([
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
            'estado' => 'activo',
            'id_rol' => $rol->id_rol,
        ]);

        $categoria = Categoria::query()->create([
            'nombre' => 'Bebidas',
        ]);
        $producto = Producto::query()->create([
            'nombre' => 'Cola',
            'precio' => 2.5,
            'stock' => 8,
            'id_categoria' => $categoria->id_categoria,
        ]);
        $codigo = CodigoQr::query()->create([
            'numero_qr' => 7,
            'codigo_qr' => 'QR-007',
        ]);
        $mesa = Mesa::query()->create([
            'numero_mesa' => 7,
            'id_qr' => $codigo->id_qr,
        ]);
        $pedido = Pedido::query()->create([
            'fecha' => now(),
            'estado' => 'pendiente',
            'total' => 5,
            'id_mesa' => $mesa->id_mesa,
        ]);
        $detalle = DetallePedido::query()->create([
            'cantidad' => 2,
            'precio_unitario' => 2.5,
            'subtotal' => 5,
            'id_pedido' => $pedido->id_pedido,
            'id_producto' => $producto->id_producto,
        ]);
        $promocion = Promocion::query()->create([
            'nombre' => '2x1',
            'descuento' => 10,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDay()->toDateString(),
            'id_usuario' => $usuario->id_usuario,
        ]);
        $producto->promociones()->attach($promocion->id_promocion);

        $reporte = Reporte::query()->create([
            'tipo_reporte' => 'ventas',
            'fecha_generacion' => now(),
            'archivo_pdf' => 'ventas.pdf',
            'id_usuario' => $usuario->id_usuario,
        ]);
        $historial = Historial::query()->create([
            'accion' => 'login',
            'fecha' => now(),
            'descripcion' => 'Inicio de sesión',
            'id_usuario' => $usuario->id_usuario,
        ]);
        $evento = Evento::query()->create([
            'nombre' => 'Noche',
            'fecha' => now()->toDateString(),
            'hora' => '20:00',
            'id_usuario' => $usuario->id_usuario,
        ]);
        $movimiento = MovimientoInventario::query()->create([
            'tipo_movimiento' => 'entrada',
            'cantidad' => 5,
            'fecha' => now(),
            'id_producto' => $producto->id_producto,
            'id_usuario' => $usuario->id_usuario,
        ]);

        $this->assertTrue($usuario->reportes()->whereKey($reporte->id_reporte)->exists());
        $this->assertTrue($usuario->historiales()->whereKey($historial->id_historial)->exists());
        $this->assertTrue($usuario->promociones()->whereKey($promocion->id_promocion)->exists());
        $this->assertTrue($usuario->eventos()->whereKey($evento->id_evento)->exists());
        $this->assertTrue($usuario->movimientosInventario()->whereKey($movimiento->id_movimiento)->exists());
        $this->assertTrue($reporte->usuario()->is($usuario));
        $this->assertTrue($historial->usuario()->is($usuario));
        $this->assertTrue($evento->usuario()->is($usuario));
        $this->assertTrue($promocion->usuario()->is($usuario));
        $this->assertTrue($promocion->productos()->whereKey($producto->id_producto)->exists());
        $this->assertTrue($producto->promociones()->whereKey($promocion->id_promocion)->exists());
        $this->assertTrue($producto->movimientosInventario()->whereKey($movimiento->id_movimiento)->exists());
        $this->assertTrue($producto->detallesPedido()->whereKey($detalle->id_detalle)->exists());
        $this->assertTrue($movimiento->producto()->is($producto));
        $this->assertTrue($movimiento->usuario()->is($usuario));
        $this->assertTrue($pedido->mesa()->is($mesa));
        $this->assertTrue($pedido->detalles()->whereKey($detalle->id_detalle)->exists());
        $this->assertTrue($detalle->pedido()->is($pedido));
        $this->assertTrue($detalle->producto()->is($producto));
        $this->assertTrue($mesa->pedidos()->whereKey($pedido->id_pedido)->exists());
    }
}
