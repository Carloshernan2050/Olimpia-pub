<?php

namespace App\Exceptions\Inventario;

use App\Exceptions\ExcepcionDeDominio;

class ProductoConPedidosException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'No se puede eliminar el producto porque tiene pedidos asociados.';
}
