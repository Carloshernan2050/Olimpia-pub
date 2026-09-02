<?php

namespace App\Exceptions\Inventario;

use App\Exceptions\ExcepcionDeDominio;

class StockInsuficienteException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'No hay stock suficiente para registrar el movimiento.';
}
