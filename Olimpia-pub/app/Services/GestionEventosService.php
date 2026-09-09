<?php

namespace App\Services;

use App\Contracts\Repositories\EventoRepositoryInterface;
use App\Contracts\Services\AlmacenamientoImagenPublicaInterface;
use App\Contracts\Services\GestionEventosServiceInterface;
use App\DTOs\Dashboard\EventoGestionDatos;
use App\DTOs\Dashboard\GuardarEventoDatos;
use App\Exceptions\Evento\EventoNoEncontradoException;
use App\Models\Evento;
use Illuminate\Http\UploadedFile;

class GestionEventosService implements GestionEventosServiceInterface
{
    use AplicaImagenPublica;

    /**
     * Inyecta el repositorio y el almacenamiento de imágenes.
     */
    public function __construct(
        private readonly EventoRepositoryInterface $eventoRepository,
        private readonly AlmacenamientoImagenPublicaInterface $imagenes,
    ) {}

    /**
     * Persiste un evento nuevo, con imagen si se envió.
     */
    public function crear(
        GuardarEventoDatos $datos,
        int $idUsuario,
        ?UploadedFile $imagen = null,
    ): EventoGestionDatos {
        return EventoGestionDatos::fromModel(
            $this->eventoRepository->create(
                $this->conImagen($this->imagenes, $datos->paraCrear($idUsuario), $imagen)
            )
        );
    }

    /**
     * Actualiza los datos de un evento y reemplaza la imagen si hay una nueva.
     */
    public function actualizar(
        int $id,
        GuardarEventoDatos $datos,
        ?UploadedFile $imagen = null,
    ): EventoGestionDatos {
        $actual = $this->obtenerModelo($id);

        return EventoGestionDatos::fromModel(
            $this->eventoRepository->update(
                $actual,
                $this->conImagen($this->imagenes, $datos->paraActualizar(), $imagen, $actual->url_imagen)
            )
        );
    }

    /**
     * Elimina un evento y su imagen.
     */
    public function eliminar(int $id): void
    {
        $actual = $this->obtenerModelo($id);
        $this->imagenes->eliminar($actual->url_imagen);
        $this->eventoRepository->delete($actual);
    }

    /**
     * Devuelve el evento si existe.
     */
    public function buscar(int $id): ?EventoGestionDatos
    {
        $evento = $this->eventoRepository->findById($id);

        return $evento === null ? null : EventoGestionDatos::fromModel($evento);
    }

    /**
     * Recorre todos los eventos para el listado del modal.
     *
     * @return list<EventoGestionDatos>
     */
    public function listar(): array
    {
        return $this->eventoRepository
            ->todas()
            ->map(fn (Evento $evento): EventoGestionDatos => EventoGestionDatos::fromModel($evento))
            ->values()
            ->all();
    }

    /**
     * Carga el modelo o lanza si no existe.
     */
    private function obtenerModelo(int $id): Evento
    {
        $evento = $this->eventoRepository->findById($id);

        if ($evento === null) {
            throw new EventoNoEncontradoException;
        }

        return $evento;
    }
}
