<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends OlimpiaModel
{
    protected $table = 'categoria';

    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function productos(): HasMany
    {
        return $this->tieneMuchosPor(Producto::class, 'id_categoria');
    }
}
