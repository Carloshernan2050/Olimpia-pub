<?php

namespace App\Exceptions\Autenticacion;

use App\Exceptions\ExcepcionDeDominio;

class RolNoConfiguradoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'El rol requerido no está configurado.';
}
