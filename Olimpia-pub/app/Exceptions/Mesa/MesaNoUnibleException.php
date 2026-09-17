<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class MesaNoUnibleException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'Solo se pueden unir mesas. La barra no forma grupos.';
}
