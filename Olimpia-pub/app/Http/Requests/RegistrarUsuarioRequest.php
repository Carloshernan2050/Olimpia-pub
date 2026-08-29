<?php

namespace App\Http\Requests;

use App\DTOs\Autenticacion\RegistrarUsuarioDatos;
use App\Exceptions\Autenticacion\CorreoYaRegistradoException;
use Illuminate\Foundation\Http\FormRequest;

class RegistrarUsuarioRequest extends FormRequest
{
    use AutorizaFormularioPublico;

    private const MAX_NOMBRE = 'max:100';

    /**
     * Normaliza los campos opcionales vacíos a nulo antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'segundo_nombre' => $this->filled('segundo_nombre') ? $this->input('segundo_nombre') : null,
            'segundo_apellido' => $this->filled('segundo_apellido') ? $this->input('segundo_apellido') : null,
        ]);
    }

    /**
     * Define las reglas de validación del formulario de registro.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'primer_nombre' => ['required', 'string', self::MAX_NOMBRE],
            'segundo_nombre' => ['nullable', 'string', self::MAX_NOMBRE],
            'primer_apellido' => ['required', 'string', self::MAX_NOMBRE],
            'segundo_apellido' => ['nullable', 'string', self::MAX_NOMBRE],
            'correo' => ['required', 'string', 'email', 'max:150', 'unique:usuario,correo'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
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
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'correo.unique' => CorreoYaRegistradoException::mensajePorDefecto(),
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'La confirmación de contraseña no coincide.',
            ...$this->mensajesCorreoYContrasena(),
        ];
    }

    /**
     * Convierte los datos validados en un DTO de registro.
     */
    public function datos(): RegistrarUsuarioDatos
    {
        return RegistrarUsuarioDatos::fromValidated($this->safe()->only([
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'correo',
            'contrasena',
        ]));
    }
}
