<?php

namespace App\Services;

use App\Contracts\Services\AutorizacionInventarioServiceInterface;
use App\Contracts\Services\NavegacionDashboardServiceInterface;
use App\DTOs\Dashboard\AccionCabeceraDatos;
use App\DTOs\Dashboard\ItemNavegacionDatos;
use App\Models\Usuario;
use Illuminate\Http\Request;

class NavegacionDashboardService implements NavegacionDashboardServiceInterface
{
    /**
     * @var array<string, string>
     */
    private const RUTAS_POR_SECCION = [
        'dashboard' => 'inicio',
        'promociones' => 'promociones',
        'menu' => 'carta',
        'inventario' => 'inventario',
        'eventos' => 'eventos',
        'eventos.detalle' => 'eventos',
    ];

    /**
     * Inyecta la petición y la autorización de inventario.
     */
    public function __construct(
        private readonly Request $request,
        private readonly AutorizacionInventarioServiceInterface $autorizacionInventario,
    ) {}

    /**
     * Ítems de la barra secundaria. La etiqueta abre Promociones; el megáfono, Eventos; el plato, el Menú; el portapapeles, Inventario.
     *
     * @return list<ItemNavegacionDatos>
     */
    public function items(): array
    {
        return [
            new ItemNavegacionDatos('inicio', 'Inicio', 'inicio', 'dashboard'),
            new ItemNavegacionDatos('promociones', 'Promociones', 'etiqueta', 'promociones'),
            new ItemNavegacionDatos('eventos', 'Eventos', 'megafono', 'eventos'),
            new ItemNavegacionDatos('carta', 'Comida y bebida', 'comida', 'menu'),
            new ItemNavegacionDatos(
                'inventario',
                'Inventario',
                'portapapeles',
                $this->puedeVerInventario() ? 'inventario' : null,
            ),
            new ItemNavegacionDatos('mesas', 'Mesas', 'mesa'),
            new ItemNavegacionDatos('analitica', 'Analítica', 'grafica'),
            new ItemNavegacionDatos('actividades', 'Actividades', 'estiramiento'),
            new ItemNavegacionDatos('historial', 'Historial', 'historial'),
        ];
    }

    /**
     * Acciones del header: carrito, mapa, QR, ajustes y perfil.
     *
     * @return list<AccionCabeceraDatos>
     */
    public function accionesCabecera(): array
    {
        return [
            new AccionCabeceraDatos('carrito', 'Carrito', 'carrito'),
            new AccionCabeceraDatos('ubicacion', 'Ubicación', 'ubicacion'),
            new AccionCabeceraDatos('qr', 'Código QR', 'qr'),
            new AccionCabeceraDatos('ajustes', 'Ajustes', 'ajustes'),
            new AccionCabeceraDatos('perfil', 'Perfil', 'perfil', true),
        ];
    }

    /**
     * Clave de la sección activa según el nombre de la ruta.
     */
    public function seccionActiva(): string
    {
        $ruta = $this->request->route()?->getName();

        return self::RUTAS_POR_SECCION[$ruta] ?? '';
    }

    /**
     * El inventario solo aparece para empleados y roles superiores.
     */
    private function puedeVerInventario(): bool
    {
        $usuario = $this->request->user();

        return $usuario instanceof Usuario
            && $this->autorizacionInventario->puedeAcceder($usuario);
    }
}
