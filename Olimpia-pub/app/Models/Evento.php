<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends OlimpiaModel
{
    protected $table = 'evento';

    protected $primaryKey = 'id_evento';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha',
        'hora',
        'estado',
        'id_usuario',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->pertenecePor(Usuario::class, 'id_usuario');
    }
}
