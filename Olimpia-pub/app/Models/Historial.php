<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historial extends Model
{
    protected $table = 'historial';

    protected $primaryKey = 'id_historial';

    public $timestamps = false;

    protected $fillable = [
        'accion',
        'fecha',
        'descripcion',
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
            'fecha' => 'datetime',
        ];
    }

    /**
     * Relación: el historial pertenece a un usuario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
