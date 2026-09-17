<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class MesaConPedidosException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'No se puede eliminar la mesa porque tiene pedidos asociados.';
}
