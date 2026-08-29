<?php

namespace App\Services;

use App\Contracts\Services\NavegacionDashboardServiceInterface;
use App\DTOs\Dashboard\AccionCabeceraDatos;
use App\DTOs\Dashboard\ItemNavegacionDatos;
use Illuminate\Http\Request;

class NavegacionDashboardService implements NavegacionDashboardServiceInterface
{
    /**
     * @var array<string, string>
     */
    private const RUTAS_POR_SECCION = [
        'dashboard' => 'inicio',
        'promociones' => 'promociones',
    ];

    /**
     * Inyecta la petición actual para resolver la sección activa.
     */
    public function __construct(
        private readonly Request $request,
    ) {}

    /**
     * Ítems de la barra secundaria. Home y Promociones ya tienen pantalla.
     *
     * @return list<ItemNavegacionDatos>
     */
    public function items(): array
    {
        return [
            new ItemNavegacionDatos('inicio', 'Inicio', 'inicio', 'dashboard'),
            new ItemNavegacionDatos('productos', 'Productos', 'etiqueta'),
            new ItemNavegacionDatos('promociones', 'Promociones', 'megafono', 'promociones'),
            new ItemNavegacionDatos('inventario', 'Inventario', 'herramienta'),
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
}
