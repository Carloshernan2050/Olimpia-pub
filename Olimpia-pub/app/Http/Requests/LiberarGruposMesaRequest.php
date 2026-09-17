<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\GuardarLiberarGruposDatos;
use Illuminate\Foundation\Http\FormRequest;

class LiberarGruposMesaRequest extends FormRequest
{
    use AutorizaUsuarioAutenticado;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'grupos' => ['required', 'array', 'min:1'],
            'grupos.*' => ['integer', 'distinct', 'exists:grupo_mesa,id_grupo'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'grupos.required' => 'Selecciona al menos un grupo para liberarlo.',
            'grupos.array' => 'Selecciona los grupos que quieres liberar.',
            'grupos.min' => 'Selecciona al menos un grupo para liberarlo.',
            'grupos.*.integer' => 'El grupo seleccionado no es válido.',
            'grupos.*.distinct' => 'No puedes seleccionar el mismo grupo dos veces.',
            'grupos.*.exists' => 'Uno de los grupos seleccionados no existe.',
        ];
    }

    /**
     * Convierte los datos validados en el DTO de persistencia.
     */
    public function datos(): GuardarLiberarGruposDatos
    {
        return GuardarLiberarGruposDatos::fromValidated($this->validated());
    }
}
