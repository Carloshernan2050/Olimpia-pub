<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use Illuminate\Foundation\Http\FormRequest;

class GuardarPedidoMesaRequest extends FormRequest
{
    /**
     * El cliente pide desde el QR, sin sesión.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'lineas' => ['required', 'array', 'min:1'],
            'lineas.*.id_producto' => ['required', 'integer', 'min:1'],
            'lineas.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lineas.required' => 'Agrega al menos un producto al pedido.',
            'lineas.array' => 'El pedido no es válido.',
            'lineas.min' => 'Agrega al menos un producto al pedido.',
            'lineas.*.id_producto.required' => 'Hay un producto sin identificar.',
            'lineas.*.id_producto.integer' => 'Hay un producto inválido.',
            'lineas.*.id_producto.min' => 'Hay un producto inválido.',
            'lineas.*.cantidad.required' => 'Indica la cantidad de cada producto.',
            'lineas.*.cantidad.integer' => 'La cantidad debe ser un entero.',
            'lineas.*.cantidad.min' => 'La cantidad debe ser mayor que cero.',
            'lineas.*.cantidad.max' => 'La cantidad no puede superar 99.',
        ];
    }

    /**
     * Pedido listo para persistir; la mesa la resuelve el servicio.
     */
    public function datos(): GuardarPedidoMesaDatos
    {
        return new GuardarPedidoMesaDatos(0, $this->lineas());
    }

    /**
     * @return list<array{id_producto: int, cantidad: int}>
     */
    private function lineas(): array
    {
        $lineas = $this->validated()['lineas'] ?? [];

        return array_values(array_map(
            fn (array $linea): array => [
                'id_producto' => (int) $linea['id_producto'],
                'cantidad' => (int) $linea['cantidad'],
            ],
            $lineas,
        ));
    }
}
