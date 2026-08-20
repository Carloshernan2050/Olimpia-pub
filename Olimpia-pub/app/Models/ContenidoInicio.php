<?php

namespace App\Models;

use App\Enums\PosicionInicio;
use App\Enums\TipoBloqueInicio;

class ContenidoInicio extends OlimpiaModel
{
    protected $table = 'contenido_inicio';

    protected $primaryKey = 'id_contenido_inicio';

    protected $fillable = [
        'posicion',
        'tipo',
        'titulo',
        'cuerpo',
        'url_media',
        'orden',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'posicion' => PosicionInicio::class,
            'tipo' => TipoBloqueInicio::class,
            'orden' => 'integer',
        ];
    }
}
