<?php

namespace App\Http\Requests;

trait AutorizaUsuarioAutenticado
{
    /**
     * El middleware auth ya protege la ruta; aquí se confirma el usuario.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }
}
