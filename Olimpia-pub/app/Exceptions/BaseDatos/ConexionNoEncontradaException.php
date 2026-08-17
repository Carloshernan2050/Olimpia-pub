<?php

namespace App\Exceptions\BaseDatos;

use App\Exceptions\ExcepcionConContexto;

class ConexionNoEncontradaException extends ExcepcionConContexto
{
    protected const PLANTILLA = 'No existe la conexión de base de datos [%s].';
}
