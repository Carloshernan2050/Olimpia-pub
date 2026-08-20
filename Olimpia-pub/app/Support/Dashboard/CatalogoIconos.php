<?php

namespace App\Support\Dashboard;

use InvalidArgumentException;

final class CatalogoIconos
{
    /**
     * Devuelve el SVG del icono solicitado.
     */
    public function render(string $nombre): string
    {
        return match ($nombre) {
            'balon' => $this->svgBalon(),
            'buscar' => $this->envolver($this->trazoBuscar()),
            'carrito' => $this->envolver($this->trazoCarrito()),
            'ubicacion' => $this->envolver($this->trazoUbicacion()),
            'qr' => $this->envolver($this->trazoQr()),
            'ajustes' => $this->envolver($this->trazoAjustes()),
            'perfil' => $this->envolver($this->trazoPerfil()),
            'inicio' => $this->envolver($this->trazoInicio()),
            'etiqueta' => $this->envolver($this->trazoEtiqueta()),
            'megafono' => $this->envolver($this->trazoMegafono()),
            'herramienta' => $this->envolver($this->trazoHerramienta()),
            'portapapeles' => $this->envolver($this->trazoPortapapeles()),
            'pesa' => $this->envolver($this->trazoPesa()),
            'grafica' => $this->envolver($this->trazoGrafica()),
            'estiramiento' => $this->envolver($this->trazoEstiramiento()),
            'historial' => $this->envolver($this->trazoHistorial()),
            'imagen' => $this->envolver($this->trazoImagen()),
            default => throw new InvalidArgumentException("El icono {$nombre} no existe."),
        };
    }

    private function envolver(string $trazos): string
    {
        $apertura = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"';
        $apertura .= ' fill="none" stroke="currentColor" stroke-width="1.8"';
        $apertura .= ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';

        return $apertura.$trazos.'</svg>';
    }

    private function svgBalon(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"';
        $svg .= ' aria-hidden="true">';
        $svg .= '<circle cx="12" cy="12" r="9.2" fill="#1e6fea"/>';
        $svg .= '<circle cx="12" cy="12" r="9.2" fill="none" stroke="#ffffff"';
        $svg .= ' stroke-width="1.4"/>';
        $svg .= '<path d="M12 8.2 14.4 10l-.9 2.8h-3l-.9-2.8L12 8.2Z" fill="#ffffff"/>';
        $svg .= '<path d="M12 3.2 9.6 8.2M12 3.2l2.4 5M4.8 8.4l4.8 1.6M19.2 8.4 14.4 10';
        $svg .= 'M3.8 14.4l4.8-1.6M20.2 14.4l-4.8-1.6M8 18.8l1.5-3.6M16 18.8l-1.5-3.6"';
        $svg .= ' fill="none" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>';

        return $svg.'</svg>';
    }

    private function trazoBuscar(): string
    {
        return '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.6-3.6"/>';
    }

    private function trazoCarrito(): string
    {
        return '<circle cx="9" cy="20" r="1.2"/><circle cx="17" cy="20" r="1.2"/>'
            .'<path d="M3 4h2l2.2 11.2a1.6 1.6 0 0 0 1.6 1.3h8.7'
            .'a1.6 1.6 0 0 0 1.6-1.3L21 8H7"/>';
    }

    private function trazoUbicacion(): string
    {
        return '<path d="M12 21s7-6.2 7-11.2A7 7 0 0 0 5 9.8C5 14.8 12 21 12 21Z"/>'
            .'<circle cx="12" cy="9.8" r="2.3"/>';
    }

    private function trazoQr(): string
    {
        return '<path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4z"/>'
            .'<path d="M14 14h2.5v2.5H14zM18.5 14H20v2.5h-1.5z'
            .'M14 18.5h2.5V20H14zM18.5 18.5H20V20h-1.5z"/>';
    }

    private function trazoAjustes(): string
    {
        return '<circle cx="12" cy="12" r="3"/>'
            .'<path d="M12 3.5v2.2M12 18.3V21M4.8 6.5l1.6 1.6M17.6 15.9l1.6 1.6'
            .'M3.5 12h2.2M18.3 12H21M4.8 17.5l1.6-1.6M17.6 8.1l1.6-1.6"/>';
    }

    private function trazoPerfil(): string
    {
        return '<circle cx="12" cy="8" r="3.2"/>'
            .'<path d="M5 19.2c1.4-3.2 3.8-4.7 7-4.7s5.6 1.5 7 4.7"/>';
    }

    private function trazoInicio(): string
    {
        return '<path d="m4 11 8-7 8 7"/><path d="M6 10.5V20h12v-9.5"/>';
    }

    private function trazoEtiqueta(): string
    {
        return '<path d="M20.2 13.2 13 20.4a1.5 1.5 0 0 1-2.1 0L3.6 13A1.5 1.5 0 0 1'
            .' 3.2 12V4.7A1.5 1.5 0 0 1 4.7 3.2H12a1.5 1.5 0 0 1 1 .4l7.2 7.3'
            .'a1.5 1.5 0 0 1 0 2.3Z"/><circle cx="8.2" cy="8.2" r="1.1"/>';
    }

    private function trazoMegafono(): string
    {
        return '<path d="m4 10 12-5v14L4 14v-4Z"/><path d="M8 14.5V19"/>'
            .'<path d="M16 8.5c1.8.9 3 2.6 3 4.5s-1.2 3.6-3 4.5"/>';
    }

    private function trazoHerramienta(): string
    {
        return '<path d="M14.5 5.2a3.4 3.4 0 0 1 4.3 4.3L15 13.3l-4.3-4.3 3.8-3.8Z"/>'
            .'<path d="m10.7 9-6.2 6.2a1.6 1.6 0 0 0 2.3 2.3L13 11.3"/>';
    }

    private function trazoPortapapeles(): string
    {
        return '<rect x="6" y="5" width="12" height="15" rx="1.6"/>'
            .'<path d="M9 5.2V4.4A1.4 1.4 0 0 1 10.4 3h3.2A1.4 1.4 0 0 1 15 4.4v.8"/>'
            .'<path d="M9 10h6M9 14h6"/>';
    }

    private function trazoPesa(): string
    {
        return '<path d="M6.5 9v6M17.5 9v6"/>'
            .'<rect x="3.2" y="8" width="3.4" height="8" rx="1"/>'
            .'<rect x="17.4" y="8" width="3.4" height="8" rx="1"/>'
            .'<path d="M6.5 12h11"/>';
    }

    private function trazoGrafica(): string
    {
        return '<path d="M4 19h16"/><path d="m5 14 4-4 3 3 7-7"/><path d="M16 6h3v3"/>';
    }

    private function trazoEstiramiento(): string
    {
        return '<circle cx="12" cy="5.2" r="1.7"/>'
            .'<path d="M8 10.5 12 9l4 1.5M12 9.2v5.2"/>'
            .'<path d="M8.2 20.2 12 14.4 15.8 20.2"/>'
            .'<path d="M6.5 7.8 9.2 10M17.5 7.8 14.8 10"/>';
    }

    private function trazoHistorial(): string
    {
        return '<path d="M4.5 12a7.5 7.5 0 1 0 2.2-5.3"/>'
            .'<path d="M4.5 5.5v4h4"/><path d="M12 8.5V12l2.5 1.6"/>';
    }

    private function trazoImagen(): string
    {
        return '<rect x="3.5" y="5" width="17" height="14" rx="1.6"/>'
            .'<circle cx="9" cy="10" r="1.5"/>'
            .'<path d="m21 16-4.8-4.8-8.2 8.3"/>';
    }
}
