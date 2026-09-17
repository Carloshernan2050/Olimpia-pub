<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class MesaNoEncontradaException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'La mesa no existe.';
}
