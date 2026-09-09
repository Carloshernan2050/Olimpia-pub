<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;

class AlmacenamientoImagenEvento extends AlmacenamientoImagenPublica
{
    /**
     * Guarda las imágenes de eventos en eventos/.
     */
    public function __construct(Filesystem $disco)
    {
        parent::__construct($disco, 'eventos');
    }
}
