<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\GuardarGrupoMesaDatos;
use Illuminate\Foundation\Http\FormRequest;

class GuardarGrupoMesaRequest extends FormRequest
{
    use AutorizaUsuarioAutenticado;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'mesas' => ['required', 'array', 'min:2'],
            'mesas.*' => ['integer', 'distinct', 'exists:mesa,id_mesa'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mesas.required' => 'Selecciona al menos dos mesas para unirlas.',
            'mesas.array' => 'Selecciona las mesas que forman el grupo.',
            'mesas.min' => 'Selecciona al menos dos mesas para unirlas.',
            'mesas.*.integer' => 'La mesa seleccionada no es válida.',
            'mesas.*.distinct' => 'No puedes seleccionar la misma mesa dos veces.',
            'mesas.*.exists' => 'Una de las mesas seleccionadas no existe.',
        ];
    }

    /**
     * Convierte los datos validados en el DTO de persistencia.
     */
    public function datos(): GuardarGrupoMesaDatos
    {
        return GuardarGrupoMesaDatos::fromValidated($this->validated());
    }
}
