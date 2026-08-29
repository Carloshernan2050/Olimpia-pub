<?php

namespace App\Exceptions\Promocion;

use App\Exceptions\ExcepcionDeDominio;

class PromocionNoEncontradaException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'La promoción no existe.';
}
