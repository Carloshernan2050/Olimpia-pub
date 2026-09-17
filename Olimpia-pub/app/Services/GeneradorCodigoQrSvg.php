<?php

namespace App\Services;

use App\Contracts\Services\GeneradorCodigoQrInterface;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class GeneradorCodigoQrSvg implements GeneradorCodigoQrInterface
{
    /**
     * Convierte un código persistido en un SVG de QR.
     */
    public function svg(string $codigo): string
    {
        $escritor = new Writer(
            new ImageRenderer(
                new RendererStyle(180, 2),
                new SvgImageBackEnd,
            )
        );

        $svg = $escritor->writeString($codigo);

        return (string) preg_replace('/^<\?xml[^>]*>\s*/', '', $svg);
    }
}
