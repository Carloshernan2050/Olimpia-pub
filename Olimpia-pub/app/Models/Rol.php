<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends OlimpiaModel
{
    protected $table = 'rol';

    protected $primaryKey = 'id_rol';

    protected $fillable = [
        'nombre_rol',
    ];

    public function usuarios(): HasMany
    {
        return $this->tieneMuchosPor(Usuario::class, 'id_rol');
    }
}
