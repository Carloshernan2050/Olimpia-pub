<?php

namespace App\Http\Requests;

use Illuminate\Http\UploadedFile;

trait ObtieneImagenSubida
{
    /**
     * Archivo de imagen validado, si el usuario envió uno.
     */
    public function imagenSubida(): ?UploadedFile
    {
        $imagen = $this->file('imagen');

        return $imagen instanceof UploadedFile ? $imagen : null;
    }
}
