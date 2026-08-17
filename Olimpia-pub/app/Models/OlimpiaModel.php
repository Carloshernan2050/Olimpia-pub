<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

abstract class OlimpiaModel extends Model
{
    use RelacionesPorClave;

    public $timestamps = false;

    /**
     * Relación belongsToMany sobre una tabla pivote.
     *
     * @param  class-string<Model>  $related
     */
    protected function muchosAMuchosPor(
        string $related,
        string $tabla,
        string $claveLocal,
        string $claveForanea
    ): BelongsToMany {
        return $this->belongsToMany($related, $tabla, $claveLocal, $claveForanea);
    }
}
