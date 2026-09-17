<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class GrupoInsuficienteException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'Selecciona al menos dos mesas para unirlas.';
}
