<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;

class AlmacenamientoImagenPromocion extends AlmacenamientoImagenPublica
{
    /**
     * Guarda las imágenes de promociones en promociones/.
     */
    public function __construct(Filesystem $disco)
    {
        parent::__construct($disco, 'promociones');
    }
}
