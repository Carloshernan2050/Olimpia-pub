<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;

class AlmacenamientoImagenProducto extends AlmacenamientoImagenPublica
{
    /**
     * Guarda las imágenes de productos en productos/.
     */
    public function __construct(Filesystem $disco)
    {
        parent::__construct($disco, 'productos');
    }
}
