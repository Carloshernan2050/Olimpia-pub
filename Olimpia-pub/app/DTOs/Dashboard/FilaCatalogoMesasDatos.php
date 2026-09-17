<?php

namespace App\DTOs\Dashboard;

use App\Enums\TipoMesa;

final readonly class FilaCatalogoMesasDatos
{
    /**
     * Fila de la lista: una mesa suelta o un grupo unido.
     */
    public function __construct(
        public int $id,
        public bool $esGrupo,
        public string $etiqueta,
        public string $subtitulo,
        public TipoMesa $tipo,
        public bool $ocupada,
        public ?PedidoMesaDatos $pedidoActivo,
        public int $orden,
    ) {}

    public static function fromMesa(MesaTarjetaDatos $mesa): self
    {
        return new self(
            $mesa->id,
            false,
            $mesa->etiqueta(),
            $mesa->tipo->etiqueta(),
            $mesa->tipo,
            $mesa->ocupada,
            $mesa->pedidoActivo,
            $mesa->numero,
        );
    }

    public static function fromGrupo(GrupoMesaDatos $grupo): self
    {
        return new self(
            $grupo->id,
            true,
            $grupo->etiqueta(),
            TipoMesa::Grupo->etiqueta(),
            TipoMesa::Grupo,
            $grupo->ocupada,
            $grupo->pedidoActivo,
            $grupo->orden(),
        );
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
     * @param  array<string, string>  $filtroQuery
     * @return array<string, int|string>
     */
    public function queryVer(array $filtroQuery): array
    {
        return [...$filtroQuery, $this->esGrupo ? 'ver_grupo' : 'ver' => $this->id];
    }

    /**
     * @param  array<string, string>  $filtroQuery
     * @return array<string, int|string>
     */
    public function queryEditar(array $filtroQuery): array
    {
        return [...$filtroQuery, $this->esGrupo ? 'editar_grupo' : 'editar' => $this->id];
    }

    /**
     * @param  array<string, string>  $filtroQuery
     * @return array<string, int|string>
     */
    public function queryPedido(array $filtroQuery): array
    {
        return [...$filtroQuery, $this->esGrupo ? 'pedido_grupo' : 'pedido' => $this->id];
    }

    public function rutaEliminar(): string
    {
        return $this->esGrupo
            ? route('mesas.grupos.eliminar', $this->id)
            : route('mesas.eliminar', $this->id);
    }

    public function mensajeEliminar(): string
    {
        return $this->esGrupo
            ? '¿Separar '.$this->etiqueta.'?'
            : '¿Eliminar '.$this->etiqueta.'?';
    }

    public function rutaTerminarPedido(): string
    {
        return $this->esGrupo
            ? route('mesas.grupos.pedido.terminar', $this->id)
            : route('mesas.pedido.terminar', $this->id);
    }

    public function mensajeTerminar(): string
    {
        return '¿Terminar el pedido de '.$this->etiqueta.'?';
    }
}
