<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\GuardarProductoInventarioDatos;
use Illuminate\Foundation\Http\FormRequest;

class GuardarProductoInventarioRequest extends FormRequest
{
    use AutorizaUsuarioAutenticado;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150', 'unique:producto,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'id_categoria' => ['required', 'integer', 'exists:categoria,id_categoria'],
            'estado' => ['nullable', 'in:activo,inactivo'],
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
            'nombre.unique' => 'Ya existe un producto con ese nombre.',
            'descripcion.max' => 'La descripción no puede superar los 255 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'id_categoria.required' => 'La categoría es obligatoria.',
            'id_categoria.exists' => 'La categoría seleccionada no existe.',
            'estado.in' => 'El estado no es válido.',
        ];
    }

    /**
     * Convierte los datos validados en el DTO de persistencia.
     */
    public function datos(): GuardarProductoInventarioDatos
    {
        return GuardarProductoInventarioDatos::fromValidated($this->validated());
    }
}
