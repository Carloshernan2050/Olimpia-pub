<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
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

    /**
     * Define los atributos que deben convertirse a otro tipo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'contrasena' => 'hashed',
        ];
    }

    /**
     * Devuelve el nombre de la columna que identifica al usuario autenticado.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id_usuario';
    }

    /**
     * Devuelve el hash de la contraseña usado por el sistema de autenticación.
     */
    public function getAuthPassword(): string
    {
        return (string) $this->contrasena;
    }

    /**
     * Devuelve el nombre de la columna de contraseña.
     */
    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    /**
     * Relación: el usuario pertenece a un rol.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    /**
     * Relación: el usuario tiene muchos reportes.
     */
    public function reportes(): HasMany
    {
        return $this->hasMany(Reporte::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: el usuario tiene muchos historiales.
     */
    public function historiales(): HasMany
    {
        return $this->hasMany(Historial::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: el usuario tiene muchas promociones.
     */
    public function promociones(): HasMany
    {
        return $this->hasMany(Promocion::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: el usuario tiene muchos eventos.
     */
    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: el usuario tiene muchos movimientos de inventario.
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'id_usuario', 'id_usuario');
    }
}
