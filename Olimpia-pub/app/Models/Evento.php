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

    /**
     * Define los atributos que deben convertirse a otro tipo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    /**
     * Relación: el evento pertenece a un usuario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
