<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\GuardarMesaDatos;
use App\Enums\TipoMesa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarMesaRequest extends FormRequest
{
    use AutorizaUsuarioAutenticado;
    use IdentificadorDeConsulta;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $idMesa = $this->identificadorPositivo($this->route('mesa'));
        $numeroUnico = Rule::unique('mesa', 'numero_mesa');

        if ($idMesa !== null) {
            $numeroUnico = $numeroUnico->ignore($idMesa, 'id_mesa');
        }

        return [
            'numero_mesa' => ['required', 'integer', 'min:1', 'max:9999', $numeroUnico],
            'tipo' => ['required', Rule::in(TipoMesa::valoresPersistibles())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'numero_mesa.required' => 'El número de mesa es obligatorio.',
            'numero_mesa.integer' => 'El número de mesa debe ser un entero.',
            'numero_mesa.min' => 'El número de mesa debe ser mayor que cero.',
            'numero_mesa.max' => 'El número de mesa no es válido.',
            'numero_mesa.unique' => 'Ya existe una mesa con ese número.',
            'tipo.required' => 'El tipo de mesa es obligatorio.',
            'tipo.in' => 'El tipo de mesa no es válido.',
        ];
    }

    /**
     * Convierte los datos validados en el DTO de persistencia.
     */
    public function datos(): GuardarMesaDatos
    {
        return GuardarMesaDatos::fromValidated($this->validated());
    }
}
