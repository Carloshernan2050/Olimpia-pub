<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\FiltroInventarioDatos;
use Illuminate\Foundation\Http\FormRequest;

class ConsultarInventarioRequest extends FormRequest
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
     * Filtro de búsqueda, categoría y estado del catálogo.
     */
    public function filtro(): FiltroInventarioDatos
    {
        return FiltroInventarioDatos::fromInput(
            $this->query('busqueda'),
            $this->query('categoria'),
            $this->query('estado'),
            $this->query('page'),
        );
    }

    /**
     * Identificador del movimiento a editar, si viene en la consulta.
     */
    public function idEdicion(): ?int
    {
        return $this->identificadorPositivo($this->query('editar'));
    }

    /**
     * Identificador del producto a consultar en el modal.
     */
    public function idVer(): ?int
    {
        return $this->identificadorPositivo($this->query('ver'));
    }

    /**
     * Identificador del producto para prellenar el formulario de movimiento.
     */
    public function idProductoPrefill(): ?int
    {
        return $this->identificadorPositivo($this->query('producto'));
    }

    /**
     * Identificador del producto a editar, si viene en la consulta.
     */
    public function idEdicionProducto(): ?int
    {
        return $this->identificadorPositivo($this->query('editar_producto'));
    }

    /**
     * Indica si el modal de CRUD debe abrirse al cargar.
     */
    public function debeAbrirModal(): bool
    {
        return $this->idEdicion() !== null
            || $this->idVer() !== null
            || $this->idProductoPrefill() !== null
            || $this->idEdicionProducto() !== null
            || $this->boolean('nueva');
    }
}
