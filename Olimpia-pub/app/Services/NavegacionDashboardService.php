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
        'inventario' => 'inventario',
    ];

    /**
     * Inyecta la petición y la autorización de inventario.
     */
    public function __construct(
        private readonly Request $request,
        private readonly AutorizacionInventarioServiceInterface $autorizacionInventario,
    ) {}

    /**
     * Ítems de la barra secundaria. Home, Promociones e Inventario ya tienen pantalla.
     *
     * @return list<ItemNavegacionDatos>
     */
    public function items(): array
    {
        $items = [
            new ItemNavegacionDatos('inicio', 'Inicio', 'inicio', 'dashboard'),
            new ItemNavegacionDatos('productos', 'Productos', 'etiqueta'),
            new ItemNavegacionDatos('promociones', 'Promociones', 'megafono', 'promociones'),
        ];

        if ($this->puedeVerInventario()) {
            $items[] = new ItemNavegacionDatos('inventario', 'Inventario', 'herramienta', 'inventario');
        }

        return [
            ...$items,
            new ItemNavegacionDatos('reportes', 'Reportes', 'portapapeles'),
            new ItemNavegacionDatos('eventos', 'Eventos', 'pesa'),
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
