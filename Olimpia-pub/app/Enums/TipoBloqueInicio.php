<?php

namespace App\Enums;

enum TipoBloqueInicio: string
{
    case Texto = 'texto';
    case Video = 'video';
    case Imagen = 'imagen';
}
