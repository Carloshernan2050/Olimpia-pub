<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends OlimpiaModel
{
    protected $table = 'movimiento_inventario';

    protected $primaryKey = 'id_movimiento';

    protected $fillable = [
        'tipo_movimiento',
        'cantidad',
        'fecha',
        'id_producto',
        'id_usuario',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'cantidad' => 'integer',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->pertenecePor(Producto::class, 'id_producto');
    }

    public function usuario(): BelongsTo
    {
        return $this->pertenecePor(Usuario::class, 'id_usuario');
    }
}
