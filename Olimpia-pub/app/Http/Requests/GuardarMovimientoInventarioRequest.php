<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\GuardarMovimientoInventarioDatos;
use App\Enums\TipoMovimientoInventario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarMovimientoInventarioRequest extends FormRequest
{
    use AutorizaUsuarioAutenticado;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id_producto' => ['required', 'integer', 'exists:producto,id_producto'],
            'tipo_movimiento' => ['required', Rule::in(TipoMovimientoInventario::valores())],
            'cantidad' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_producto.required' => 'El producto es obligatorio.',
            'id_producto.exists' => 'El producto seleccionado no existe.',
            'tipo_movimiento.required' => 'El tipo de movimiento es obligatorio.',
            'tipo_movimiento.in' => 'El tipo de movimiento no es válido.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }

    /**
     * Convierte los datos validados en el DTO de persistencia.
     */
    public function datos(): GuardarMovimientoInventarioDatos
    {
        return GuardarMovimientoInventarioDatos::fromValidated($this->validated());
    }
}
