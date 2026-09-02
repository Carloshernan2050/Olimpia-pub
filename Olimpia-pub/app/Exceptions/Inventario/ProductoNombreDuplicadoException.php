<?php

namespace App\Exceptions\Inventario;

use App\Exceptions\ExcepcionDeDominio;

class ProductoNombreDuplicadoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'Ya existe un producto con ese nombre.';
}
