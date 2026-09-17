<?php

namespace App\Services;

use App\Contracts\Repositories\GrupoMesaRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Services\CatalogoMesasServiceInterface;
use App\DTOs\Dashboard\CatalogoMesasDatos;
use App\DTOs\Dashboard\FilaCatalogoMesasDatos;
use App\DTOs\Dashboard\FiltroMesasDatos;
use App\DTOs\Dashboard\GrupoMesaDatos;
use App\DTOs\Dashboard\MesaTarjetaDatos;
use App\DTOs\Dashboard\MesaUnibleDatos;
use App\DTOs\Dashboard\TipoMesaDatos;
use App\Enums\TipoMesa;
use App\Models\GrupoMesa;
use App\Models\Mesa;

class CatalogoMesasService implements CatalogoMesasServiceInterface
{
    /**
     * Inyecta mesas sueltas y grupos unidos.
     */
    public function __construct(
        private readonly MesaRepositoryInterface $mesaRepository,
        private readonly GrupoMesaRepositoryInterface $grupoMesaRepository,
    ) {}

    /**
     * Convierte mesas y uniones en filas de la lista.
     */
    public function obtenerCatalogo(?FiltroMesasDatos $filtro = null): CatalogoMesasDatos
    {
        $filtro ??= FiltroMesasDatos::predeterminado();

        $grupos = $this->grupos();

        return new CatalogoMesasDatos(
            $this->filas($filtro->tipo, $grupos),
            TipoMesaDatos::catalogo(),
            TipoMesaDatos::persistibles(),
            $this->mesasUnibles(),
            $grupos,
            $this->mesaRepository->siguienteNumero(),
        );
    }

    /**
     * @param  list<GrupoMesaDatos>  $grupos
     * @return list<FilaCatalogoMesasDatos>
     */
    private function filas(?TipoMesa $tipo, array $grupos): array
    {
        $filasDeGrupos = array_map(
            fn (GrupoMesaDatos $grupo): FilaCatalogoMesasDatos => FilaCatalogoMesasDatos::fromGrupo($grupo),
            $grupos,
        );

        $filas = match (true) {
            $tipo?->esGrupo() === true => $filasDeGrupos,
            $tipo?->esPedidosActivos() === true => $this->filasConPedidoActivo($filasDeGrupos),
            $tipo !== null => $this->filasDeMesas($tipo),
            default => [...$filasDeGrupos, ...$this->filasDeMesas(null)],
        };

        usort(
            $filas,
            fn (FilaCatalogoMesasDatos $izquierda, FilaCatalogoMesasDatos $derecha): int => $izquierda->orden <=> $derecha->orden,
        );

        return $filas;
    }

    /**
     * Mesas sueltas y grupos que tienen un pedido en curso.
     *
     * @param  list<FilaCatalogoMesasDatos>  $filasDeGrupos
     * @return list<FilaCatalogoMesasDatos>
     */
    private function filasConPedidoActivo(array $filasDeGrupos): array
    {
        return array_values(array_filter(
            [...$filasDeGrupos, ...$this->filasDeMesas(null)],
            fn (FilaCatalogoMesasDatos $fila): bool => $fila->ocupada,
        ));
    }

    /**
     * @return list<FilaCatalogoMesasDatos>
     */
    private function filasDeMesas(?TipoMesa $tipo): array
    {
        return $this->mesaRepository
            ->catalogoSinGrupo($tipo)
            ->map(fn (Mesa $mesa): FilaCatalogoMesasDatos => FilaCatalogoMesasDatos::fromMesa(
                MesaTarjetaDatos::fromModel($mesa),
            ))
            ->values()
            ->all();
    }

    /**
     * @return list<GrupoMesaDatos>
     */
    private function grupos(): array
    {
        return $this->grupoMesaRepository
            ->catalogo()
            ->map(fn (GrupoMesa $grupo): GrupoMesaDatos => GrupoMesaDatos::fromModel($grupo))
            ->values()
            ->all();
    }

    /**
     * @return list<MesaUnibleDatos>
     */
    private function mesasUnibles(): array
    {
        return $this->mesaRepository
            ->disponiblesParaUnir()
            ->map(fn (Mesa $mesa): MesaUnibleDatos => new MesaUnibleDatos(
                (int) $mesa->id_mesa,
                (int) $mesa->numero_mesa,
            ))
            ->values()
            ->all();
    }
}
