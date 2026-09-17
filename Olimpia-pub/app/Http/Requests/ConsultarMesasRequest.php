<?php

namespace App\Http\Requests;

use App\DTOs\Dashboard\FiltroMesasDatos;
use Illuminate\Foundation\Http\FormRequest;

class ConsultarMesasRequest extends FormRequest
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
     * Filtro de tipo del catálogo.
     */
    public function filtro(): FiltroMesasDatos
    {
        return FiltroMesasDatos::fromInput($this->query('tipo'));
    }

    /**
     * Identificador de la mesa a editar.
     */
    public function idEdicion(): ?int
    {
        return $this->identificadorPositivo($this->query('editar'));
    }

    /**
     * Identificador de la mesa a consultar.
     */
    public function idVer(): ?int
    {
        return $this->identificadorPositivo($this->query('ver'));
    }

    /**
     * Identificador de la mesa cuyo pedido se abre en el modal.
     */
    public function idPedido(): ?int
    {
        return $this->identificadorPositivo($this->query('pedido'));
    }

    /**
     * Identificador del grupo a consultar.
     */
    public function idVerGrupo(): ?int
    {
        return $this->identificadorPositivo($this->query('ver_grupo'));
    }

    /**
     * Identificador del grupo a editar.
     */
    public function idEdicionGrupo(): ?int
    {
        return $this->identificadorPositivo($this->query('editar_grupo'));
    }

    /**
     * Identificador del grupo cuyo pedido se abre en el modal.
     */
    public function idPedidoGrupo(): ?int
    {
        return $this->identificadorPositivo($this->query('pedido_grupo'));
    }

    /**
     * Indica si el formulario de unir mesas debe abrirse.
     */
    public function debeUnir(): bool
    {
        return $this->boolean('unir');
    }

    /**
     * Indica si el formulario de liberar mesas debe abrirse.
     */
    public function debeLiberar(): bool
    {
        return $this->boolean('liberar');
    }

    /**
     * Indica si el modal debe abrirse al cargar.
     */
    public function debeAbrirModal(): bool
    {
        return $this->idEdicion() !== null
            || $this->idVer() !== null
            || $this->idPedido() !== null
            || $this->idVerGrupo() !== null
            || $this->idEdicionGrupo() !== null
            || $this->idPedidoGrupo() !== null
            || $this->debeUnir()
            || $this->debeLiberar()
            || $this->boolean('nueva');
    }
}
