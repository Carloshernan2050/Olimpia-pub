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

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'total' => 'decimal:2',
        ];
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'id_mesa', 'id_mesa');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }
}
