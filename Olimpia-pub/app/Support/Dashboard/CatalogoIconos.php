<?php

namespace App\Support\Dashboard;

use InvalidArgumentException;

final class CatalogoIconos
{
    /**
     * Trazos de iconos de línea (se envuelven en el SVG compartido).
     *
     * @var array<string, string>
     */
    private const TRAZOS = [
        'buscar' => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.6-3.6"/>',
        'carrito' => '<circle cx="8.4" cy="20.1" r="1.15"/><circle cx="16.2" cy="20.1" r="1.15"/>'
            .'<path d="M2.6 4.4h2l2.1 10.4a1.4 1.4 0 0 0 1.4 1.15h7.8'
            .'a1.4 1.4 0 0 0 1.4-1.15L19.4 8.6H6.5"/>'
            .'<path d="M19.6 2.4v4.2M17.5 4.5h4.2"/>',
        'ubicacion' => '<path d="M12 21.2s6.8-6 6.8-11A6.8 6.8 0 0 0 5.2 10.2'
            .'C5.2 15.2 12 21.2 12 21.2Z"/>'
            .'<circle cx="12" cy="10" r="2.2"/>',
        'qr' => '<path d="M3.4 3.4h6.4v6.4H3.4z"/><path d="M5.6 5.6h2v2h-2z"/>'
            .'<path d="M14.2 3.4h6.4v6.4h-6.4z"/><path d="M16.4 5.6h2v2h-2z"/>'
            .'<path d="M3.4 14.2h6.4v6.4H3.4z"/><path d="M5.6 16.4h2v2h-2z"/>'
            .'<path d="M14.2 14.2h2.3v2.3h-2.3zM17.8 14.2h2.8v2.3h-2.8z'
            .'M14.2 17.8h2.8v2.8h-2.8zM18.6 18.4h2V21"/>',
        'ajustes' => '<circle cx="12" cy="12" r="2.8"/>'
            .'<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25'
            .'a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1'
            .'a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38'
            .'a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0'
            .' 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0'
            .'l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0'
            .' 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38'
            .'a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4'
            .'a2 2 0 0 0-2-2z"/>',
        'perfil' => '<circle cx="12" cy="12" r="9.1"/>'
            .'<circle cx="12" cy="10" r="2.8"/>'
            .'<path d="M6.4 18.1c1.4-2.5 3.6-3.7 5.6-3.7s4.2 1.2 5.6 3.7"/>',
        'inicio' => '<path d="m3.8 11 8.2-7.2 8.2 7.2"/><path d="M6.2 10.2V20h11.6V10.2"/>',
        'etiqueta' => '<path d="M20.1 13.1 12.9 20.3a1.5 1.5 0 0 1-2.1 0L3.6 13.1A1.5 1.5 0 0 1'
            .' 3.2 12V4.8A1.5 1.5 0 0 1 4.8 3.2H12a1.5 1.5 0 0 1 1.1.4l7 7.2'
            .'a1.5 1.5 0 0 1 0 2.3Z"/><circle cx="8.1" cy="8.1" r="1.05"/>',
        'megafono' => '<path d="m3.8 10.2 12.2-5.4v14.4L3.8 13.8v-3.6Z"/><path d="M8.2 14.6V19.4"/>'
            .'<path d="M16 8.6c1.8.9 3 2.6 3 4.4s-1.2 3.5-3 4.4"/>',
        'herramienta' => '<path d="M14.5 5.2a3.4 3.4 0 0 1 4.3 4.3L15 13.3l-4.3-4.3 3.8-3.8Z"/>'
            .'<path d="m10.7 9-6.2 6.2a1.6 1.6 0 0 0 2.3 2.3L13 11.3"/>',
        'surtidor' => '<rect x="10" y="2.6" width="4" height="11.2" rx="1.3"/>'
            .'<path d="M9.2 2.6h5.6"/><path d="M10.4 5.4h3.2"/>'
            .'<circle cx="12" cy="18.2" r="3.3"/>'
            .'<path d="M10.2 18.2h3.6"/>',
        'portapapeles' => '<rect x="6.2" y="5.2" width="11.6" height="15.2" rx="1.6"/>'
            .'<path d="M9.1 5.2V4.3A1.3 1.3 0 0 1 10.4 3h3.2A1.3 1.3 0 0 1 14.9 4.3v.9"/>'
            .'<path d="M9.2 10.2h5.6M9.2 13.8h5.6"/>',
        'pesa' => '<path d="M6.5 9v6M17.5 9v6"/>'
            .'<rect x="3.2" y="8" width="3.4" height="8" rx="1"/>'
            .'<rect x="17.4" y="8" width="3.4" height="8" rx="1"/>'
            .'<path d="M6.5 12h11"/>',
        'mesa' => '<ellipse cx="12" cy="7.4" rx="8.4" ry="3.1"/>'
            .'<path d="M6.8 10 5 20.2"/><path d="M17.2 10 19 20.2"/>'
            .'<path d="M12 10.4v10.2"/>',
        'grafica' => '<path d="M5 19V12.2M9.2 19V8.4M13.4 19v-5.2"/>'
            .'<path d="m4.6 14.2 4.6-4.8 3.2 2.4 6.4-6.2"/><path d="M16.2 5.4h3.4v3.4"/>',
        'estiramiento' => '<circle cx="12" cy="4.6" r="2"/>'
            .'<path d="M4.2 10.2h15.6"/><path d="M12 6.8v6.8"/>'
            .'<path d="M12 13.6 7 20.6M12 13.6l5 7"/>',
        'historial' => '<path d="M5.2 8.4A8 8 0 1 1 4.4 13"/>'
            .'<path d="M4.6 4.6v4.1h4.1"/><path d="M13.2 8.8V13l2.6 1.6"/>',
        'caja' => '<path d="M3.5 8.2 12 4l8.5 4.2-8.5 4.2L3.5 8.2Z"/>'
            .'<path d="M3.5 8.2V16L12 20.2V12.4"/>'
            .'<path d="M20.5 8.2V16L12 20.2"/>',
        'tendencia-baja' => '<path d="M4 5v14h16"/>'
            .'<path d="m7 8 4 4 3-3 5 6"/>'
            .'<path d="M16 15h3v3"/>',
        'carrito-alerta' => '<circle cx="9" cy="20" r="1.2"/><circle cx="17" cy="20" r="1.2"/>'
            .'<path d="M3 4h2l2.2 11.2a1.6 1.6 0 0 0 1.6 1.3h8.7'
            .'a1.6 1.6 0 0 0 1.6-1.3L21 8H7"/>'
            .'<path d="M17.2 3.2v3.4M17.2 8.4h.01"/>',
        'ojo' => '<path d="M2.8 12S6.2 6.5 12 6.5 21.2 12 21.2 12 17.8 17.5 12 17.5 2.8 12 2.8 12Z"/>'
            .'<circle cx="12" cy="12" r="2.6"/>',
        'imagen' => '<rect x="3.5" y="5" width="17" height="14" rx="1.6"/>'
            .'<circle cx="9" cy="10" r="1.5"/>'
            .'<path d="m21 16-4.8-4.8-8.2 8.3"/>',
        'filtro' => '<path d="M4 5h16l-6.2 7.5v5l-3.6 1.7v-6.7L4 5z"/>',
        'mas' => '<path d="M12 5v14M5 12h14"/>',
        'cerrar' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'lapiz' => '<path d="M4 16.2V20h3.8L19 8.8 15.2 5 4 16.2Z"/><path d="m15.2 5 3.8 3.8"/>',
        'papelera' => '<path d="M5 7h14M10 7V5h4v2M8 7v12h8V7"/>',
        'comida' => '<path d="M2.5 12.4c.2-3.2 2.3-5.1 4.9-5.1s4.7 1.9 4.9 5.1"/>'
            .'<path d="M2.4 12.4h10"/>'
            .'<path d="M2.5 15.4c.3 2.1 2.3 3.3 4.9 3.3s4.6-1.2 4.9-3.3"/>'
            .'<path d="M18.6 3.5 17.4 8.7"/>'
            .'<path d="M14 8.7h7.6"/>'
            .'<path d="M14.6 8.7 15.5 19.5h4.8l.9-10.8"/>',
    ];

    private const SVG_APERTURA = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"';

    private const SVG_CIERRE = '</svg>';

    /**
     * Iconos con SVG propio (no usan el trazo de línea compartido).
     *
     * @var array<string, string>
     */
    private const SVG_PROPIOS = [
        'balon' => self::SVG_APERTURA
            .' aria-hidden="true">'
            .'<circle cx="12" cy="12" r="9.2" fill="currentColor"/>'
            .'<circle cx="12" cy="12" r="9.2" fill="none" stroke="#ffffff"'
            .' stroke-width="1.4"/>'
            .'<path d="M12 8.2 14.4 10l-.9 2.8h-3l-.9-2.8L12 8.2Z" fill="#ffffff"/>'
            .'<path d="M12 3.2 9.6 8.2M12 3.2l2.4 5M4.8 8.4l4.8 1.6M19.2 8.4 14.4 10'
            .'M3.8 14.4l4.8-1.6M20.2 14.4l-4.8-1.6M8 18.8l1.5-3.6M16 18.8l-1.5-3.6"'
            .' fill="none" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>'
            .self::SVG_CIERRE,
    ];

    /**
     * Devuelve el SVG del icono solicitado.
     */
    public function render(string $nombre): string
    {
        if (isset(self::SVG_PROPIOS[$nombre])) {
            return self::SVG_PROPIOS[$nombre];
        }

        if (! isset(self::TRAZOS[$nombre])) {
            throw new InvalidArgumentException("El icono {$nombre} no existe.");
        }

        return $this->envolver(self::TRAZOS[$nombre]);
    }

    private function envolver(string $trazos): string
    {
        $apertura = self::SVG_APERTURA;
        $apertura .= ' fill="none" stroke="currentColor" stroke-width="1.8"';
        $apertura .= ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';

        return $apertura.$trazos.self::SVG_CIERRE;
    }
}
