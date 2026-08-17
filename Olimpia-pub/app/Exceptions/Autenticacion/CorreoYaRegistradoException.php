<?php

namespace App\Exceptions\Autenticacion;

use App\Exceptions\ExcepcionDeDominio;

class CorreoYaRegistradoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'El correo ya está registrado.';
}
