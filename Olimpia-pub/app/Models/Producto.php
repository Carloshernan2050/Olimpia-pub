<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends OlimpiaModel
{
    protected $table = 'producto';

    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'estado',
        'id_categoria',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->pertenecePor(Categoria::class, 'id_categoria');
    }

    public function promociones(): BelongsToMany
    {
        return $this->muchosAMuchosPor(
            Promocion::class,
            'producto_promocion',
            'id_producto',
            'id_promocion'
        );
    }

    public function movimientosInventario(): HasMany
    {
        return $this->tieneMuchosPor(MovimientoInventario::class, 'id_producto');
    }

    public function detallesPedido(): HasMany
    {
        return $this->tieneMuchosPor(DetallePedido::class, 'id_producto');
    }
}
