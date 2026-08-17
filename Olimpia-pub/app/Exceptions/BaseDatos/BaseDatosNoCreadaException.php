<?php

namespace App\Exceptions\BaseDatos;

use App\Exceptions\ExcepcionDeDominio;

class BaseDatosNoCreadaException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'No se pudo verificar o crear la base de datos. '
        .'Revisa que MySQL esté encendido y las credenciales del .env.';
}
