<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class MesaNumeroDuplicadoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'Ya existe una mesa con ese número.';
}
