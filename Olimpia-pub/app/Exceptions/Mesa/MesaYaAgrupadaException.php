<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class MesaYaAgrupadaException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'Hay una mesa que ya pertenece a otro grupo.';
}
