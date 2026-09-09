<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventoRepositoryInterface;
use App\DTOs\Dashboard\FiltroRangoFechasDatos;
use App\Models\Evento;
use Illuminate\Support\Collection;

class EloquentEventoRepository extends EloquentRepository implements EventoRepositoryInterface
{
    /**
     * Crea un evento con los datos recibidos.
     */
    public function create(array $data): Evento
    {
        /** @var Evento */
        return $this->createModel($data);
    }

    /**
     * Actualiza un evento existente.
     */
    public function update(Evento $evento, array $data): Evento
    {
        $evento->update($data);

        return $evento->fresh() ?? $evento;
    }

    /**
     * Elimina un evento.
     */
    public function delete(Evento $evento): void
    {
        $evento->delete();
    }

    /**
     * Busca un evento por su identificador.
     */
    public function findById(int $id): ?Evento
    {
        /** @var Evento|null */
        return $this->findFirstBy('id_evento', $id);
    }

    /**
     * Eventos cuyo día cae en el rango, ordenados por fecha y hora.
     *
     * @return Collection<int, Evento>
     */
    public function listar(?FiltroRangoFechasDatos $filtro = null): Collection
    {
        $filtro ??= FiltroRangoFechasDatos::predeterminado();
        $consulta = $this->newQuery();

        if ($filtro->desde !== null) {
            $consulta->whereDate('fecha', '>=', $filtro->desde);
        }

        if ($filtro->hasta !== null) {
            $consulta->whereDate('fecha', '<=', $filtro->hasta);
        }

        return $consulta
            ->orderBy('fecha')
            ->orderBy('hora')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Todos los eventos ordenados por nombre.
     *
     * @return Collection<int, Evento>
     */
    public function todas(): Collection
    {
        return $this->newQuery()
            ->orderBy('nombre')
            ->get();
    }

    /**
     * @return class-string<Evento>
     */
    protected function modelClass(): string
    {
        return Evento::class;
    }
}
