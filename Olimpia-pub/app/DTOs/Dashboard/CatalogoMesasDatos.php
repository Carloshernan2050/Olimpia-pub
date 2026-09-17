<?php

namespace App\DTOs\Dashboard;

final readonly class CatalogoMesasDatos
{
    /**
     * @param  list<FilaCatalogoMesasDatos>  $filas
     * @param  list<TipoMesaDatos>  $tipos
     * @param  list<TipoMesaDatos>  $tiposPersistibles
     * @param  list<MesaUnibleDatos>  $mesasUnibles
     * @param  list<GrupoMesaDatos>  $grupos
     * @param  int  $siguienteNumero
     */
    public function __construct(
        public array $filas,
        public array $tipos,
        public array $tiposPersistibles = [],
        public array $mesasUnibles = [],
        public array $grupos = [],
        public int $siguienteNumero = 1,
    ) {}

    public function tieneMesas(): bool
    {
        return $this->filas !== [];
    }

    /**
     * @return list<FilaCatalogoMesasDatos>
     */
    public function enOrden(): array
    {
        return $this->filas;
    }

    /**
     * Hace falta al menos un par de mesas sueltas para unir.
     */
    public function puedeUnir(): bool
    {
        return count($this->mesasUnibles) >= 2;
    }

    /**
     * Hay uniones que se pueden separar.
     */
    public function puedeLiberar(): bool
    {
        return $this->grupos !== [];
    }

    /**
     * Mesas que se pueden marcar al unir o editar un grupo.
     *
     * @return list<MesaUnibleDatos>
     */
    public function opcionesUnir(?GrupoMesaDatos $grupo = null): array
    {
        $opciones = $this->mesasUnibles;
        $ids = array_map(fn (MesaUnibleDatos $mesa): int => $mesa->id, $opciones);

        if ($grupo !== null) {
            foreach ($grupo->mesas as $mesa) {
                if (! in_array($mesa->id, $ids, true)) {
                    $opciones[] = new MesaUnibleDatos($mesa->id, $mesa->numero);
                }
            }
        }

        usort(
            $opciones,
            fn (MesaUnibleDatos $izquierda, MesaUnibleDatos $derecha): int => $izquierda->numero <=> $derecha->numero,
        );

        return $opciones;
    }
}
