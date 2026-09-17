<?php

namespace App\Services;

use App\Contracts\Repositories\GrupoMesaRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Services\EnlacePedidoMesaInterface;
use App\Contracts\Services\GeneradorCodigoQrInterface;
use App\Contracts\Services\GestionGruposMesaServiceInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\DTOs\Dashboard\GrupoMesaDatos;
use App\DTOs\Dashboard\GuardarGrupoMesaDatos;
use App\DTOs\Dashboard\GuardarLiberarGruposDatos;
use App\Enums\TipoMesa;
use App\Exceptions\Mesa\GrupoConVariosPedidosException;
use App\Exceptions\Mesa\GrupoInsuficienteException;
use App\Exceptions\Mesa\GrupoNoEncontradoException;
use App\Exceptions\Mesa\PedidoActivoNoEncontradoException;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Exceptions\Mesa\MesaNoUnibleException;
use App\Exceptions\Mesa\MesaYaAgrupadaException;
use App\Models\GrupoMesa;
use App\Models\Mesa;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;

class GestionGruposMesaService implements GestionGruposMesaServiceInterface
{
    /**
     * Inyecta grupos, mesas y el generador de QR.
     */
    public function __construct(
        private readonly GrupoMesaRepositoryInterface $grupoMesaRepository,
        private readonly MesaRepositoryInterface $mesaRepository,
        private readonly GeneradorCodigoQrInterface $generadorQr,
        private readonly EnlacePedidoMesaInterface $enlacePedido,
        private readonly GestionPedidosServiceInterface $gestionPedidos,
        private readonly ConnectionInterface $conexion,
    ) {}

    /**
     * Une mesas de tipo mesa en un grupo.
     */
    public function unir(GuardarGrupoMesaDatos $datos): GrupoMesaDatos
    {
        $mesas = $this->mesasParaUnir($datos->idsMesas);

        $grupo = $this->conexion->transaction(function () use ($mesas): GrupoMesa {
            $grupo = $this->grupoMesaRepository->create(['estado' => 'activo']);
            $this->mesaRepository->asignarGrupo(
                $mesas->pluck('id_mesa')->map(fn ($id): int => (int) $id)->all(),
                (int) $grupo->id_grupo,
            );

            return $this->obtenerModelo((int) $grupo->id_grupo);
        });

        return $this->datos($grupo);
    }

    /**
     * Cambia las mesas que forman el grupo.
     */
    public function actualizar(int $id, GuardarGrupoMesaDatos $datos): GrupoMesaDatos
    {
        $grupo = $this->obtenerModelo($id);
        $mesas = $this->mesasParaUnir($datos->idsMesas, (int) $grupo->id_grupo);
        $nuevos = $mesas->pluck('id_mesa')->map(fn ($idMesa): int => (int) $idMesa)->all();
        $anteriores = $grupo->mesas->pluck('id_mesa')->map(fn ($idMesa): int => (int) $idMesa)->all();

        $actualizado = $this->conexion->transaction(function () use ($grupo, $anteriores, $nuevos): GrupoMesa {
            $this->mesaRepository->asignarGrupo(array_values(array_diff($anteriores, $nuevos)), null);
            $this->mesaRepository->asignarGrupo($nuevos, (int) $grupo->id_grupo);

            return $this->obtenerModelo((int) $grupo->id_grupo);
        });

        return $this->datos($actualizado);
    }

    /**
     * Separa el grupo; las mesas vuelven a listarse solas.
     */
    public function separar(int $id): void
    {
        $grupo = $this->obtenerModelo($id);

        $this->conexion->transaction(function () use ($grupo): void {
            $this->persistirSeparacion($grupo);
        });
    }

    /**
     * Libera las uniones seleccionadas.
     */
    public function liberar(GuardarLiberarGruposDatos $datos): void
    {
        if ($datos->idsGrupos === []) {
            throw new GrupoNoEncontradoException;
        }

        $this->conexion->transaction(function () use ($datos): void {
            foreach ($datos->idsGrupos as $id) {
                $this->persistirSeparacion($this->obtenerModelo($id));
            }
        });
    }

    /**
     * Busca un grupo con QR de cada mesa y el pedido activo.
     */
    public function buscar(int $id): ?GrupoMesaDatos
    {
        $grupo = $this->grupoMesaRepository->findById($id);

        return $grupo === null ? null : $this->datos($grupo);
    }

    /**
     * Cierra el pedido activo del grupo y lo deja en el historial.
     */
    public function terminarPedido(int $id, int $idUsuario): void
    {
        foreach ($this->obtenerModelo($id)->mesas as $mesa) {
            if ($mesa->pedidoActivo !== null) {
                $this->gestionPedidos->terminar((int) $mesa->id_mesa, $idUsuario);

                return;
            }
        }

        throw new PedidoActivoNoEncontradoException;
    }

    /**
     * @param  list<int>  $ids
     * @return Collection<int, Mesa>
     */
    private function mesasParaUnir(array $ids, ?int $idGrupo = null): Collection
    {
        $ids = array_values(array_unique($ids));

        if (count($ids) < 2) {
            throw new GrupoInsuficienteException;
        }

        $mesas = $this->mesaRepository->findByIds($ids);

        if ($mesas->count() !== count($ids)) {
            throw new MesaNoEncontradaException;
        }

        $pedidosActivos = 0;

        foreach ($mesas as $mesa) {
            $tipo = $mesa->tipo instanceof TipoMesa
                ? $mesa->tipo
                : TipoMesa::desdeValor((string) $mesa->tipo);

            if ($tipo !== TipoMesa::Mesa) {
                throw new MesaNoUnibleException;
            }

            if ($mesa->id_grupo !== null && (int) $mesa->id_grupo !== $idGrupo) {
                throw new MesaYaAgrupadaException;
            }

            if ($mesa->pedidoActivo !== null) {
                $pedidosActivos++;
            }
        }

        if ($pedidosActivos > 1) {
            throw new GrupoConVariosPedidosException;
        }

        return $mesas;
    }

    private function persistirSeparacion(GrupoMesa $grupo): void
    {
        $this->mesaRepository->asignarGrupo(
            $grupo->mesas->pluck('id_mesa')->map(fn ($idMesa): int => (int) $idMesa)->all(),
            null,
        );
        $this->grupoMesaRepository->delete($grupo);
    }

    private function obtenerModelo(int $id): GrupoMesa
    {
        $grupo = $this->grupoMesaRepository->findById($id);

        if ($grupo === null) {
            throw new GrupoNoEncontradoException;
        }

        return $grupo;
    }

    private function datos(GrupoMesa $grupo): GrupoMesaDatos
    {
        return GrupoMesaDatos::fromModel(
            $grupo,
            fn (string $codigo): string => $this->generadorQr->svg($this->enlacePedido->url($codigo)),
        );
    }
}
