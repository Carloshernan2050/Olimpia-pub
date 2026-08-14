<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reporte extends Model
{
    protected $table = 'reporte';

    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    protected $fillable = [
        'tipo_reporte',
        'fecha_generacion',
        'archivo_pdf',
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
            'fecha_generacion' => 'datetime',
        ];
    }

    /**
     * Relación: el reporte pertenece a un usuario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
