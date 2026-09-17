<?php

namespace App\Exceptions\Mesa;

use App\Exceptions\ExcepcionDeDominio;

class MesaEnGrupoException extends ExcepcionDeDominio
{
    protected const MENSAJE = 'Separa el grupo antes de eliminar o cambiar esa mesa.';
}
