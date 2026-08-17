<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait RelacionesPorClave
{
    /**
     * Relación belongsTo usando la misma clave en ambos lados.
     *
     * @param  class-string  $related
     */
    protected function pertenecePor(string $related, string $clave): BelongsTo
    {
        return $this->belongsTo($related, $clave, $clave);
    }

    /**
     * Relación hasMany usando la misma clave en ambos lados.
     *
     * @param  class-string  $related
     */
    protected function tieneMuchosPor(string $related, string $clave): HasMany
    {
        return $this->hasMany($related, $clave, $clave);
    }

    /**
     * Relación hasOne usando la misma clave en ambos lados.
     *
     * @param  class-string  $related
     */
    protected function tieneUnoPor(string $related, string $clave): HasOne
    {
        return $this->hasOne($related, $clave, $clave);
    }
}
