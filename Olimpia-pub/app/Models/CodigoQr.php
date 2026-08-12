<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CodigoQr extends Model
{
    protected $table = 'codigo_qr';

    protected $primaryKey = 'id_qr';

    public $timestamps = false;

    protected $fillable = [
        'numero_qr',
        'estado',
        'codigo_qr',
    ];

    public function mesa(): HasOne
    {
        return $this->hasOne(Mesa::class, 'id_qr', 'id_qr');
    }
}
