<?php

namespace App\Exceptions\BaseDatos;

use App\Exceptions\ExcepcionConContexto;

class DirectorioSqliteNoCreadoException extends ExcepcionConContexto
{
    protected const PLANTILLA = 'No se pudo crear el directorio de SQLite: %s';
}
