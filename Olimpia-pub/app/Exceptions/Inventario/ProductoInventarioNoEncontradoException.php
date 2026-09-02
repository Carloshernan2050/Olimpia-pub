<?php

namespace App\Exceptions\Inventario;

use App\Exceptions\ExcepcionDeDominio;

class ProductoInventarioNoEncontradoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'El producto no existe en el inventario.';
}
