<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class GrupoConVariosPedidosException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'El grupo solo puede tener un pedido activo.';
}
