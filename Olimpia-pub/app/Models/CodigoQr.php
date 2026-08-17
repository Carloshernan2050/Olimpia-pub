<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class CodigoQr extends OlimpiaModel
{
    protected $table = 'codigo_qr';

    protected $primaryKey = 'id_qr';

    protected $fillable = [
        'numero_qr',
        'estado',
        'codigo_qr',
    ];

    public function mesa(): HasOne
    {
        return $this->tieneUnoPor(Mesa::class, 'id_qr');
    }
}
