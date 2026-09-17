<?php

namespace App\DTOs\Dashboard;

use App\Enums\TipoMesa;
use App\Models\Mesa;

final readonly class MesaTarjetaDatos
{
    /**
     * Celda del tablero de mesas, con código QR y pedido activo.
     */
    public function __construct(
        public int $id,
        public int $numero,
        public TipoMesa $tipo,
        public string $codigo,
        public string $qrSvg,
        public bool $ocupada,
        public ?PedidoMesaDatos $pedidoActivo,
    ) {}

    /**
     * Construye la tarjeta a partir de la mesa persistida.
     */
    public static function fromModel(Mesa $mesa, string $qrSvg = ''): self
    {
        $pedido = $mesa->pedidoActivo;

        return new self(
            (int) $mesa->id_mesa,
            (int) $mesa->numero_mesa,
            $mesa->tipo instanceof TipoMesa ? $mesa->tipo : TipoMesa::desdeValor((string) $mesa->tipo),
            (string) ($mesa->codigoQr?->codigo_qr ?? 'OLIMPIA-MESA-'.str_pad((string) $mesa->numero_mesa, 2, '0', STR_PAD_LEFT)),
            $qrSvg,
            $pedido !== null,
            $pedido === null ? null : PedidoMesaDatos::fromModel($pedido),
        );
    }

    public function etiqueta(): string
    {
        return 'Mesa '.$this->numero;
    }

    public function rutaTerminarPedido(): string
    {
        return route('mesas.pedido.terminar', $this->id);
    }

    public function mensajeTerminar(): string
    {
        return '¿Terminar el pedido de '.$this->etiqueta().'?';
    }

    /**
     * Texto corto del pedido para la lista.
     */
    public function resumenPedido(): string
    {
        if (! $this->ocupada) {
            return 'Sin pedido';
        }

        return implode(', ', $this->lineasDePedido(2));
    }

    /**
     * Total del pedido activo, o cero si la mesa está libre.
     */
    public function cuentaFormateada(): string
    {
        return $this->pedidoActivo?->totalFormateado() ?? '$ 0,00';
    }

    public function claseTipo(): string
    {
        return $this->tipo->clase();
    }

    /**
     * Nombres del pedido activo, o nada si la mesa está libre.
     *
     * @return list<string>
     */
    public function lineasDePedido(int $limite = 4): array
    {
        return $this->pedidoActivo?->nombresParaCelda($limite) ?? [];
    }
}
