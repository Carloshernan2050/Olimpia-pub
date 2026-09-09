<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\GuardarEventoDatos;
use App\Enums\EstadoEvento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarEventoRequest extends FormRequest
{
    use AutorizaUsuarioAutenticado;
    use ObtieneImagenSubida;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'estado' => ['nullable', Rule::in(EstadoEvento::valores())],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'descripcion.max' => 'La descripción no puede superar los 255 caracteres.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es válida.',
            'hora.required' => 'La hora es obligatoria.',
            'hora.date_format' => 'La hora no es válida.',
            'estado.in' => 'El estado no es válido.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            'imagen.max' => 'La imagen no puede superar los 2 MB.',
        ];
    }

    /**
     * Convierte los datos validados en el DTO de persistencia.
     */
    public function datos(): GuardarEventoDatos
    {
        return GuardarEventoDatos::fromValidated($this->validated());
    }
}
