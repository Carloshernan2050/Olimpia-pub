<?php

namespace App\Exceptions\BaseDatos;

use App\Exceptions\ExcepcionConContexto;

class ArchivoSqliteNoCreadoException extends ExcepcionConContexto
{
    protected const PLANTILLA = 'No se pudo crear el archivo SQLite: %s';
}
