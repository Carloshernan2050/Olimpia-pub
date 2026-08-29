<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\FiltroPromocionesDatos;
use Illuminate\Foundation\Http\FormRequest;

class ConsultarCatalogoPromocionesRequest extends FormRequest
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
     * Filtro de orden y fechas del catálogo.
     */
    public function filtro(): FiltroPromocionesDatos
    {
        return FiltroPromocionesDatos::fromInput(
            $this->query('desde'),
            $this->query('hasta'),
        );
    }

    /**
     * Identificador de la promoción a editar, si viene en la consulta.
     */
    public function idEdicion(): ?int
    {
        $id = $this->query('editar');

        if (! is_numeric($id) || (int) $id < 1) {
            return null;
        }

        return (int) $id;
    }

    /**
     * Indica si el modal de CRUD debe abrirse al cargar.
     */
    public function debeAbrirModal(): bool
    {
        return $this->idEdicion() !== null || $this->boolean('nueva');
    }
}
