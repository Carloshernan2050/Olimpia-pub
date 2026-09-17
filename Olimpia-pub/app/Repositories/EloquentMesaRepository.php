<?php

namespace App\Repositories;

use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Enums\TipoMesa;
use App\Models\Mesa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentMesaRepository extends EloquentRepository implements MesaRepositoryInterface
{
    /**
     * Crea una mesa con los datos recibidos.
     */
    public function create(array $data): Mesa
    {
        /** @var Mesa */
        return $this->createModel($data);
    }

    /**
     * Busca una mesa por su número.
     */
    public function findByNumero(int $numeroMesa): ?Mesa
    {
        /** @var Mesa|null */
        return $this->findFirstBy('numero_mesa', $numeroMesa);
    }

    /**
     * Actualiza una mesa existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Mesa $mesa, array $data): Mesa
    {
        $mesa->update($data);

        return $mesa->fresh() ?? $mesa;
    }

    /**
     * Busca una mesa por su identificador.
     */
    public function findById(int $id): ?Mesa
    {
        /** @var Mesa|null */
        return $this->newQuery()
            ->with(['codigoQr', 'pedidoActivo.detalles.producto'])
            ->whereKey($id)
            ->first();
    }

    /**
     * Mesa del código QR activo, con pedido en curso.
     */
    public function findByCodigoQr(string $codigo): ?Mesa
    {
        /** @var Mesa|null */
        return $this->newQuery()
            ->with(['codigoQr', 'pedidoActivo.detalles.producto'])
            ->whereHas(
                'codigoQr',
                fn ($consulta) => $consulta
                    ->where('codigo_qr', $codigo)
                    ->where('estado', 'activo'),
            )
            ->first();
    }

    /**
     * Mesas del catálogo, con QR y pedido activo, opcionalmente por tipo.
     *
     * @return Collection<int, Mesa>
     */
    public function catalogo(?TipoMesa $tipo = null): Collection
    {
        return $this->consultaDeCatalogo($tipo)->get();
    }

    /**
     * Mesas que no pertenecen a un grupo, opcionalmente por tipo.
     *
     * @return Collection<int, Mesa>
     */
    public function catalogoSinGrupo(?TipoMesa $tipo = null): Collection
    {
        return $this->consultaDeCatalogo($tipo)
            ->whereNull('id_grupo')
            ->get();
    }

    /**
     * Mesas de tipo mesa que se pueden unir, o las del grupo indicado.
     *
     * @return Collection<int, Mesa>
     */
    public function disponiblesParaUnir(?int $idGrupo = null): Collection
    {
        return $this->consultaDeCatalogo(TipoMesa::Mesa)
            ->where(function ($consulta) use ($idGrupo): void {
                $consulta->whereNull('id_grupo');

                if ($idGrupo !== null) {
                    $consulta->orWhere('id_grupo', $idGrupo);
                }
            })
            ->get();
    }

    /**
     * Mesas por identificador, con QR y pedido activo.
     *
     * @param  list<int>  $ids
     * @return Collection<int, Mesa>
     */
    public function findByIds(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return $this->consultaDeCatalogo()
            ->whereKey($ids)
            ->get();
    }

    /**
     * Asigna o quita el grupo de las mesas indicadas.
     *
     * @param  list<int>  $idsMesas
     */
    public function asignarGrupo(array $idsMesas, ?int $idGrupo): void
    {
        if ($idsMesas === []) {
            return;
        }

        $this->newQuery()
            ->whereKey($idsMesas)
            ->update(['id_grupo' => $idGrupo]);
    }

    /**
     * Elimina una mesa.
     */
    public function delete(Mesa $mesa): void
    {
        $mesa->delete();
    }

    /**
     * Siguiente número libre: el mayor existente más uno, o 1 si no hay mesas.
     */
    public function siguienteNumero(): int
    {
        $maximo = $this->newQuery()->max('numero_mesa');

        return $maximo === null ? 1 : ((int) $maximo) + 1;
    }

    /**
     * @return class-string<Mesa>
     */
    protected function modelClass(): string
    {
        return Mesa::class;
    }

    /**
     * Consulta base del catálogo, con QR y pedido activo.
     */
    private function consultaDeCatalogo(?TipoMesa $tipo = null): Builder
    {
        $consulta = $this->newQuery()
            ->with(['codigoQr', 'pedidoActivo.detalles.producto'])
            ->orderBy('numero_mesa');

        if ($tipo !== null) {
            $consulta->where('tipo', $tipo->value);
        }

        return $consulta;
    }
}
