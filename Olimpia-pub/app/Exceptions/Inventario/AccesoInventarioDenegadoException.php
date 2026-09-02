<?php

namespace App\Exceptions\Inventario;

use App\Exceptions\ExcepcionDeDominio;

class AccesoInventarioDenegadoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'No tienes permiso para acceder al inventario.';
}
