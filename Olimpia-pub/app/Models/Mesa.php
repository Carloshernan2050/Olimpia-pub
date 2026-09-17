<?php

namespace App\Models;

use App\Enums\EstadoPedido;
use App\Enums\TipoMesa;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mesa extends OlimpiaModel
{
    protected $table = 'mesa';

    protected $primaryKey = 'id_mesa';

    protected $fillable = [
        'numero_mesa',
        'tipo',
        'estado',
        'id_qr',
        'id_grupo',
    ];

    protected $attributes = [
        'tipo' => 'mesa',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => TipoMesa::class,
        ];
    }

    public function codigoQr(): BelongsTo
    {
        return $this->pertenecePor(CodigoQr::class, 'id_qr');
    }

    public function grupo(): BelongsTo
    {
        return $this->pertenecePor(GrupoMesa::class, 'id_grupo');
    }

    public function pedidos(): HasMany
    {
        return $this->tieneMuchosPor(Pedido::class, 'id_mesa');
    }

    /**
     * El único pedido que la mesa puede tener en curso.
     */
    public function pedidoActivo(): HasOne
    {
        return $this->tieneUnoPor(Pedido::class, 'id_mesa')
            ->where('estado', EstadoPedido::Activo->value);
    }
}
