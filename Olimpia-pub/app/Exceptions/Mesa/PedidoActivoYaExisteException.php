<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class PedidoActivoYaExisteException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'La mesa ya tiene un pedido activo.';
}
