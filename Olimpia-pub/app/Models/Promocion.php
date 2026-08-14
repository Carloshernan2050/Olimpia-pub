<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promocion extends Model
{
    protected $table = 'promocion';

    protected $primaryKey = 'id_promocion';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'descuento',
        'fecha_inicio',
        'fecha_fin',
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
            'descuento' => 'decimal:2',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    /**
     * Relación: la promoción pertenece a un usuario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: la promoción puede aplicarse a muchos productos.
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(
            Producto::class,
            'producto_promocion',
            'id_promocion',
            'id_producto'
        );
    }
}
