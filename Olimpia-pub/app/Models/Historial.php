<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historial extends OlimpiaModel
{
    protected $table = 'historial';

    protected $primaryKey = 'id_historial';

    protected $fillable = [
        'accion',
        'fecha',
        'descripcion',
        'id_usuario',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->pertenecePor(Usuario::class, 'id_usuario');
    }
}
