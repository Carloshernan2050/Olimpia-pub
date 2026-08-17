<?php

namespace App\Exceptions\BaseDatos;

use App\Exceptions\ExcepcionDeDominio;

class DriverNoSoportadoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'Solo se soporta crear automáticamente bases sqlite, mysql o mariadb.';
}
