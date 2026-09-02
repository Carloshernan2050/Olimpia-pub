<?php

namespace App\Exceptions\Inventario;

use App\Exceptions\ExcepcionDeDominio;

class MovimientoInventarioNoEncontradoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'El movimiento de inventario no existe.';
}
