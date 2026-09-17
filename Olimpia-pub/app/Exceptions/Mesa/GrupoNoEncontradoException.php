<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class GrupoNoEncontradoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'El grupo de mesas no existe.';
}
