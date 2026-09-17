<?php

namespace App\Repositories;

use App\Contracts\Repositories\GrupoMesaRepositoryInterface;
use App\Models\GrupoMesa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentGrupoMesaRepository extends EloquentRepository implements GrupoMesaRepositoryInterface
{
    /**
     * Crea un grupo de mesas.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): GrupoMesa
    {
        /** @var GrupoMesa */
        return $this->createModel($data);
    }

    /**
     * Busca un grupo con sus mesas, QR y pedido activo.
     */
    public function findById(int $id): ?GrupoMesa
    {
        /** @var GrupoMesa|null */
        return $this->consultaConMesas()
            ->whereKey($id)
            ->first();
    }

    /**
     * Grupos del catálogo, con mesas ordenadas por número.
     *
     * @return Collection<int, GrupoMesa>
     */
    public function catalogo(): Collection
    {
        return $this->consultaConMesas()
            ->orderBy('id_grupo')
            ->get();
    }

    /**
     * Elimina el grupo; las mesas quedan sueltas.
     */
    public function delete(GrupoMesa $grupo): void
    {
        $grupo->delete();
    }

    /**
     * @return class-string<GrupoMesa>
     */
    protected function modelClass(): string
    {
        return GrupoMesa::class;
    }

    private function consultaConMesas(): Builder
    {
        return $this->newQuery()
            ->with([
                'mesas' => fn ($consulta) => $consulta->orderBy('numero_mesa'),
                'mesas.codigoQr',
                'mesas.pedidoActivo.detalles.producto',
            ]);
    }
}
