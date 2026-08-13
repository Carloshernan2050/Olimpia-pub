<?php

namespace App\Http\Requests;

use App\DTOs\Autenticacion\RegistrarUsuarioDatos;
use Illuminate\Foundation\Http\FormRequest;

class RegistrarUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'segundo_nombre' => $this->filled('segundo_nombre') ? $this->input('segundo_nombre') : null,
            'segundo_apellido' => $this->filled('segundo_apellido') ? $this->input('segundo_apellido') : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'primer_nombre' => ['required', 'string', 'max:100'],
            'segundo_nombre' => ['nullable', 'string', 'max:100'],
            'primer_apellido' => ['required', 'string', 'max:100'],
            'segundo_apellido' => ['nullable', 'string', 'max:100'],
            'correo' => ['required', 'string', 'email', 'max:150', 'unique:usuario,correo'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo no es válido.',
            'correo.unique' => 'El correo ya está registrado.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
    }

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
