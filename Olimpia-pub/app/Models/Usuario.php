<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'estado',
        'id_rol',
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected function casts(): array
    {
        return [
            'contrasena' => 'hashed',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function reportes(): HasMany
    {
        return $this->hasMany(Reporte::class, 'id_usuario', 'id_usuario');
    }

    public function historiales(): HasMany
    {
        return $this->hasMany(Historial::class, 'id_usuario', 'id_usuario');
    }

    public function promociones(): HasMany
    {
        return $this->hasMany(Promocion::class, 'id_usuario', 'id_usuario');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'id_usuario', 'id_usuario');
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'id_usuario', 'id_usuario');
    }
}
