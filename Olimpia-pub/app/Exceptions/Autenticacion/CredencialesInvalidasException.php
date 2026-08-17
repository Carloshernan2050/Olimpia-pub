<?php

namespace App\Exceptions\Autenticacion;

use App\Exceptions\ExcepcionDeDominio;

class CredencialesInvalidasException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'Correo o contraseña incorrectos.';
}
