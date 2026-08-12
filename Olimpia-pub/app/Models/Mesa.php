<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    protected $table = 'mesa';

    protected $primaryKey = 'id_mesa';

    public $timestamps = false;

    protected $fillable = [
        'numero_mesa',
        'estado',
        'id_qr',
    ];

    public function codigoQr(): BelongsTo
    {
        return $this->belongsTo(CodigoQr::class, 'id_qr', 'id_qr');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'id_mesa', 'id_mesa');
    }
}
