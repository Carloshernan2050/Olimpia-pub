<?php

namespace App\Models;

use App\Enums\EstadoPedido;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends OlimpiaModel
{
    protected $table = 'pedido';

    protected $primaryKey = 'id_pedido';

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
            'estado' => EstadoPedido::class,
        ];
    }

    public function mesa(): BelongsTo
    {
        return $this->pertenecePor(Mesa::class, 'id_mesa');
    }

    public function detalles(): HasMany
    {
        return $this->tieneMuchosPor(DetallePedido::class, 'id_pedido');
    }
}
