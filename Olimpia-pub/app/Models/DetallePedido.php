<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePedido extends OlimpiaModel
{
    protected $table = 'detalle_pedido';

    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'cantidad',
        'precio_unitario',
        'subtotal',
        'id_pedido',
        'id_producto',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->pertenecePor(Pedido::class, 'id_pedido');
    }

    public function producto(): BelongsTo
    {
        return $this->pertenecePor(Producto::class, 'id_producto');
    }
}
