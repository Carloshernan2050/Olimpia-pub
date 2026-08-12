<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends Model
{
    protected $table = 'evento';

    protected $primaryKey = 'id_evento';

    public $timestamps = false;

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
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
