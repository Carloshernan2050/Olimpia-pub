<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class PedidoActivoNoEncontradoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'La mesa no tiene un pedido activo para terminar.';
}
