<?php

namespace App\Exceptions\Evento;

use App\Exceptions\ExcepcionDeDominio;

class EventoNoEncontradoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'El evento no existe.';
}
