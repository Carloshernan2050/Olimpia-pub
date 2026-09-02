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
        'carrito' => '<circle cx="9" cy="20" r="1.2"/><circle cx="17" cy="20" r="1.2"/>'
            .'<path d="M3 4h2l2.2 11.2a1.6 1.6 0 0 0 1.6 1.3h8.7'
            .'a1.6 1.6 0 0 0 1.6-1.3L21 8H7"/>',
        'ubicacion' => '<path d="M12 21s7-6.2 7-11.2A7 7 0 0 0 5 9.8C5 14.8 12 21 12 21Z"/>'
            .'<circle cx="12" cy="9.8" r="2.3"/>',
        'qr' => '<path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4z"/>'
            .'<path d="M14 14h2.5v2.5H14zM18.5 14H20v2.5h-1.5z'
            .'M14 18.5h2.5V20H14zM18.5 18.5H20V20h-1.5z"/>',
        'ajustes' => '<circle cx="12" cy="12" r="3"/>'
            .'<path d="M12 3.5v2.2M12 18.3V21M4.8 6.5l1.6 1.6M17.6 15.9l1.6 1.6'
            .'M3.5 12h2.2M18.3 12H21M4.8 17.5l1.6-1.6M17.6 8.1l1.6-1.6"/>',
        'perfil' => '<circle cx="12" cy="8" r="3.2"/>'
            .'<path d="M5 19.2c1.4-3.2 3.8-4.7 7-4.7s5.6 1.5 7 4.7"/>',
        'inicio' => '<path d="m4 11 8-7 8 7"/><path d="M6 10.5V20h12v-9.5"/>',
        'etiqueta' => '<path d="M20.2 13.2 13 20.4a1.5 1.5 0 0 1-2.1 0L3.6 13A1.5 1.5 0 0 1'
            .' 3.2 12V4.7A1.5 1.5 0 0 1 4.7 3.2H12a1.5 1.5 0 0 1 1 .4l7.2 7.3'
            .'a1.5 1.5 0 0 1 0 2.3Z"/><circle cx="8.2" cy="8.2" r="1.1"/>',
        'megafono' => '<path d="m4 10 12-5v14L4 14v-4Z"/><path d="M8 14.5V19"/>'
            .'<path d="M16 8.5c1.8.9 3 2.6 3 4.5s-1.2 3.6-3 4.5"/>',
        'herramienta' => '<path d="M14.5 5.2a3.4 3.4 0 0 1 4.3 4.3L15 13.3l-4.3-4.3 3.8-3.8Z"/>'
            .'<path d="m10.7 9-6.2 6.2a1.6 1.6 0 0 0 2.3 2.3L13 11.3"/>',
        'portapapeles' => '<rect x="6" y="5" width="12" height="15" rx="1.6"/>'
            .'<path d="M9 5.2V4.4A1.4 1.4 0 0 1 10.4 3h3.2A1.4 1.4 0 0 1 15 4.4v.8"/>'
            .'<path d="M9 10h6M9 14h6"/>',
        'pesa' => '<path d="M6.5 9v6M17.5 9v6"/>'
            .'<rect x="3.2" y="8" width="3.4" height="8" rx="1"/>'
            .'<rect x="17.4" y="8" width="3.4" height="8" rx="1"/>'
            .'<path d="M6.5 12h11"/>',
        'grafica' => '<path d="M4 19h16"/><path d="m5 14 4-4 3 3 7-7"/><path d="M16 6h3v3"/>',
        'estiramiento' => '<circle cx="12" cy="5.2" r="1.7"/>'
            .'<path d="M8 10.5 12 9l4 1.5M12 9.2v5.2"/>'
            .'<path d="M8.2 20.2 12 14.4 15.8 20.2"/>'
            .'<path d="M6.5 7.8 9.2 10M17.5 7.8 14.8 10"/>',
        'historial' => '<path d="M4.5 12a7.5 7.5 0 1 0 2.2-5.3"/>'
            .'<path d="M4.5 5.5v4h4"/><path d="M12 8.5V12l2.5 1.6"/>',
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
    ];

    /**
     * Iconos con SVG propio (no usan el trazo de línea compartido).
     *
     * @var array<string, string>
     */
    private const SVG_PROPIOS = [
        'balon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"'
            .' aria-hidden="true">'
            .'<circle cx="12" cy="12" r="9.2" fill="#1e6fea"/>'
            .'<circle cx="12" cy="12" r="9.2" fill="none" stroke="#ffffff"'
            .' stroke-width="1.4"/>'
            .'<path d="M12 8.2 14.4 10l-.9 2.8h-3l-.9-2.8L12 8.2Z" fill="#ffffff"/>'
            .'<path d="M12 3.2 9.6 8.2M12 3.2l2.4 5M4.8 8.4l4.8 1.6M19.2 8.4 14.4 10'
            .'M3.8 14.4l4.8-1.6M20.2 14.4l-4.8-1.6M8 18.8l1.5-3.6M16 18.8l-1.5-3.6"'
            .' fill="none" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>'
            .'</svg>',
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
        $apertura = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"';
        $apertura .= ' fill="none" stroke="currentColor" stroke-width="1.8"';
        $apertura .= ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';

        return $apertura.$trazos.'</svg>';
    }
}
