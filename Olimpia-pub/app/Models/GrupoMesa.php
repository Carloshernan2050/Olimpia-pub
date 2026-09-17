<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoMesa extends OlimpiaModel
{
    protected $table = 'grupo_mesa';

    protected $primaryKey = 'id_grupo';

    protected $fillable = [
        'estado',
    ];

    /**
     * Mesas unidas en este grupo.
     */
    public function mesas(): HasMany
    {
        return $this->tieneMuchosPor(Mesa::class, 'id_grupo');
    }
}
