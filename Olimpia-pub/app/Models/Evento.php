<?php

namespace App\Models;

use App\Enums\EstadoEvento;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends OlimpiaModel
{
    protected $table = 'evento';

    protected $primaryKey = 'id_evento';

    protected $fillable = [
        'nombre',
        'descripcion',
        'url_imagen',
        'fecha',
        'hora',
        'estado',
        'id_usuario',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'estado' => EstadoEvento::class,
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->pertenecePor(Usuario::class, 'id_usuario');
    }
}
