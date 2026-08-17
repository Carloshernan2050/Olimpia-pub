<?php

namespace App\Http\Requests;

trait AutorizaFormularioPublico
{
    /**
     * Indica si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mensajes compartidos de correo y contraseña.
     *
     * @return array<string, string>
     */
    protected function mensajesCorreoYContrasena(): array
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo no es válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ];
    }
}
