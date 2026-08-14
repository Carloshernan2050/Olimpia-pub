<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'pedido';

    protected $primaryKey = 'id_pedido';

    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'estado',
        'total',
        'id_mesa',
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
            'total' => 'decimal:2',
        ];
    }

    /**
     * Relación: el pedido pertenece a una mesa.
     */
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'id_mesa', 'id_mesa');
    }

    /**
     * Relación: el pedido tiene muchos detalles.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }
}
