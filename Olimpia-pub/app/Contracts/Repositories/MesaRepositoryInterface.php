<?php

namespace App\Contracts\Repositories;

use App\Enums\TipoMesa;
use App\Models\Mesa;
use Illuminate\Support\Collection;

interface MesaRepositoryInterface
{
    /**
     * Crea una mesa con los datos recibidos.
     */
    public function create(array $data): Mesa;

    /**
     * Busca una mesa por su número.
     */
    public function findByNumero(int $numeroMesa): ?Mesa;

    /**
     * Actualiza una mesa existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Mesa $mesa, array $data): Mesa;

    /**
     * Busca una mesa por su identificador.
     */
    public function findById(int $id): ?Mesa;

    /**
     * Mesa del código QR activo, con pedido en curso.
     */
    public function findByCodigoQr(string $codigo): ?Mesa;

    /**
     * Mesas del catálogo, con QR y pedido activo, opcionalmente por tipo.
     *
     * @return Collection<int, Mesa>
     */
    public function catalogo(?TipoMesa $tipo = null): Collection;

    /**
     * Mesas que no pertenecen a un grupo, opcionalmente por tipo.
     *
     * @return Collection<int, Mesa>
     */
    public function catalogoSinGrupo(?TipoMesa $tipo = null): Collection;

    /**
     * Mesas de tipo mesa que se pueden unir, o las del grupo indicado.
     *
     * @return Collection<int, Mesa>
     */
    public function disponiblesParaUnir(?int $idGrupo = null): Collection;

    /**
     * Mesas por identificador, con QR y pedido activo.
     *
     * @param  list<int>  $ids
     * @return Collection<int, Mesa>
     */
    public function findByIds(array $ids): Collection;

    /**
     * Asigna o quita el grupo de las mesas indicadas.
     *
     * @param  list<int>  $idsMesas
     */
    public function asignarGrupo(array $idsMesas, ?int $idGrupo): void;

    /**
     * Elimina una mesa.
     */
    public function delete(Mesa $mesa): void;

    /**
     * Siguiente número libre: el mayor existente más uno, o 1 si no hay mesas.
     */
    public function siguienteNumero(): int;
}
