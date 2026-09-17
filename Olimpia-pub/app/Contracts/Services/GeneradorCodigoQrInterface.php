<?php

namespace App\Contracts\Services;

interface GeneradorCodigoQrInterface
{
    /**
     * Convierte un código persistido en un SVG de QR.
     */
    public function svg(string $codigo): string;
}
