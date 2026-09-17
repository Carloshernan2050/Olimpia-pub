<?php

namespace App\DTOs\Dashboard;

use App\Models\GrupoMesa;
use App\Models\Mesa;

final readonly class GrupoMesaDatos
{
    /**
     * @param  list<MesaTarjetaDatos>  $mesas
     */
    public function __construct(
        public int $id,
        public array $mesas,
        public bool $ocupada,
        public ?PedidoMesaDatos $pedidoActivo,
    ) {}

    /**
     * Construye el grupo a partir del modelo persistido.
     *
     * @param  callable(string): string|null  $generarQr
     */
    public static function fromModel(GrupoMesa $grupo, ?callable $generarQr = null): self
    {
        $tarjetas = $grupo->mesas
            ->sortBy('numero_mesa')
            ->values()
            ->map(function (Mesa $mesa) use ($generarQr): MesaTarjetaDatos {
                $codigo = (string) ($mesa->codigoQr?->codigo_qr ?? GuardarMesaDatos::codigoDeNumero((int) $mesa->numero_mesa));
                $svg = $generarQr === null ? '' : $generarQr($codigo);

                return MesaTarjetaDatos::fromModel($mesa, $svg);
            })
            ->all();

        $pedido = null;

        foreach ($tarjetas as $tarjeta) {
            if ($tarjeta->ocupada) {
                $pedido = $tarjeta->pedidoActivo;
                break;
            }
        }

        return new self((int) $grupo->id_grupo, $tarjetas, $pedido !== null, $pedido);
    }

    public function etiqueta(): string
    {
        return self::etiquetaDeNumeros(
            array_map(fn (MesaTarjetaDatos $mesa): int => $mesa->numero, $this->mesas),
        );
    }

    public function rutaTerminarPedido(): string
    {
        return route('mesas.grupos.pedido.terminar', $this->id);
    }

    public function mensajeTerminar(): string
    {
        return '¿Terminar el pedido de '.$this->etiqueta().'?';
    }

    /**
     * @return list<int>
     */
    public function idsMesas(): array
    {
        return array_map(fn (MesaTarjetaDatos $mesa): int => $mesa->id, $this->mesas);
    }

    /**
     * Primer número del grupo, para ordenar el catálogo.
     */
    public function orden(): int
    {
        $numeros = array_map(fn (MesaTarjetaDatos $mesa): int => $mesa->numero, $this->mesas);

        return $numeros === [] ? $this->id : min($numeros);
    }

    public function resumenPedido(): string
    {
        if (! $this->ocupada) {
            return 'Sin pedido';
        }

        return implode(', ', $this->pedidoActivo?->nombresParaCelda(2) ?? []);
    }

    public function cuentaFormateada(): string
    {
        return $this->pedidoActivo?->totalFormateado() ?? '$ 0,00';
    }

    /**
     * @param  list<int>  $numeros
     */
    public static function etiquetaDeNumeros(array $numeros): string
    {
        $numeros = array_values($numeros);

        if ($numeros === []) {
            return 'Grupo';
        }

        if (count($numeros) === 1) {
            return 'Mesa '.$numeros[0];
        }

        $ultimo = array_pop($numeros);

        return 'Mesas '.implode(', ', $numeros).' y '.$ultimo;
    }
}
