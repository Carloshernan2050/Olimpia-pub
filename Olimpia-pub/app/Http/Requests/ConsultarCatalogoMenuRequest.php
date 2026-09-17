<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\FiltroMenuDatos;
use Illuminate\Foundation\Http\FormRequest;

class ConsultarCatalogoMenuRequest extends FormRequest
{
    use AutorizaUsuarioAutenticado;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Filtro de búsqueda y categoría del menú.
     */
    public function filtro(): FiltroMenuDatos
    {
        return FiltroMenuDatos::fromInput(
            $this->query('busqueda'),
            $this->query('categoria'),
        );
    }
}
