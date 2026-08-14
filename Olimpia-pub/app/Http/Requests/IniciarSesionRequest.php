<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IniciarSesionRequest extends FormRequest
{
    /**
     * Indica si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define las reglas de validación del formulario de inicio de sesión.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'correo' => ['required', 'string', 'email', 'max:150'],
            'contrasena' => ['required', 'string'],
        ];
    }

    /**
     * Devuelve los mensajes de error de validación en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo no es válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ];
    }
}
