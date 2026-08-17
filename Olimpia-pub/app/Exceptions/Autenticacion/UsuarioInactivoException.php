<?php

namespace App\Exceptions\Autenticacion;

use App\Exceptions\ExcepcionDeDominio;

class UsuarioInactivoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'La cuenta de usuario no está activa.';
}
