<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends OlimpiaModel
{
    protected $table = 'mesa';

    protected $primaryKey = 'id_mesa';

    protected $fillable = [
        'numero_mesa',
        'estado',
        'id_qr',
    ];

    public function codigoQr(): BelongsTo
    {
        return $this->pertenecePor(CodigoQr::class, 'id_qr');
    }

    public function pedidos(): HasMany
    {
        return $this->tieneMuchosPor(Pedido::class, 'id_mesa');
    }
}
