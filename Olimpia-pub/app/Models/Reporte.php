<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reporte extends OlimpiaModel
{
    protected $table = 'reporte';

    protected $primaryKey = 'id_reporte';

    protected $fillable = [
        'tipo_reporte',
        'fecha_generacion',
        'archivo_pdf',
        'id_usuario',
    ];

    protected function casts(): array
    {
        return [
            'fecha_generacion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->pertenecePor(Usuario::class, 'id_usuario');
    }
}
