<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use RelacionesPorClave;

    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
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

    public function getAuthIdentifierName(): string
    {
        return 'id_usuario';
    }

    public function getAuthPassword(): string
    {
        return (string) $this->contrasena;
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    public function rol(): BelongsTo
    {
        return $this->pertenecePor(Rol::class, 'id_rol');
    }

    public function reportes(): HasMany
    {
        return $this->tieneMuchosPor(Reporte::class, 'id_usuario');
    }

    public function historiales(): HasMany
    {
        return $this->tieneMuchosPor(Historial::class, 'id_usuario');
    }

    public function promociones(): HasMany
    {
        return $this->tieneMuchosPor(Promocion::class, 'id_usuario');
    }

    public function eventos(): HasMany
    {
        return $this->tieneMuchosPor(Evento::class, 'id_usuario');
    }

    public function movimientosInventario(): HasMany
    {
        return $this->tieneMuchosPor(MovimientoInventario::class, 'id_usuario');
    }
}
