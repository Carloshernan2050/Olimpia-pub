<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\FiltroRangoFechasDatos;
use Illuminate\Foundation\Http\FormRequest;

class ConsultarCatalogoPromocionesRequest extends FormRequest
{
    use AutorizaUsuarioAutenticado;
    use IdentificadorDeConsulta;

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
    public function filtro(): FiltroRangoFechasDatos
    {
        return FiltroRangoFechasDatos::fromInput(
            $this->query('desde'),
            $this->query('hasta'),
        );
    }

    /**
     * Identificador de la promoción a editar, si viene en la consulta.
     */
    public function idEdicion(): ?int
    {
        return $this->identificadorPositivo($this->query('editar'));
    }

    /**
     * Indica si el modal de CRUD debe abrirse al cargar.
     */
    public function debeAbrirModal(): bool
    {
        return $this->idEdicion() !== null || $this->boolean('nueva');
    }
}
