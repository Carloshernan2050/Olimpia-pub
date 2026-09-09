<?php

namespace App\Services;

use App\Contracts\Repositories\PromocionRepositoryInterface;
use App\Contracts\Services\AlmacenamientoImagenPublicaInterface;
use App\Contracts\Services\GestionPromocionesServiceInterface;
use App\DTOs\Dashboard\GuardarPromocionDatos;
use App\DTOs\Dashboard\PromocionGestionDatos;
use App\Exceptions\Promocion\PromocionNoEncontradaException;
use App\Models\Promocion;
use Illuminate\Http\UploadedFile;

class GestionPromocionesService implements GestionPromocionesServiceInterface
{
    use AplicaImagenPublica;

    /**
     * Inyecta el repositorio y el almacenamiento de imágenes.
     */
    public function __construct(
        private readonly PromocionRepositoryInterface $promocionRepository,
        private readonly AlmacenamientoImagenPublicaInterface $imagenes,
    ) {}

    /**
     * Persiste una promoción nueva, con imagen si se envió.
     */
    public function crear(
        GuardarPromocionDatos $datos,
        int $idUsuario,
        ?UploadedFile $imagen = null,
    ): PromocionGestionDatos {
        return PromocionGestionDatos::fromModel(
            $this->promocionRepository->create(
                $this->conImagen($this->imagenes, $datos->paraCrear($idUsuario), $imagen)
            )
        );
    }

    /**
     * Actualiza los datos de una promoción y reemplaza la imagen si hay una nueva.
     */
    public function actualizar(
        int $id,
        GuardarPromocionDatos $datos,
        ?UploadedFile $imagen = null,
    ): PromocionGestionDatos {
        $actual = $this->obtenerModelo($id);

        return PromocionGestionDatos::fromModel(
            $this->promocionRepository->update(
                $actual,
                $this->conImagen($this->imagenes, $datos->paraActualizar(), $imagen, $actual->url_imagen)
            )
        );
    }

    /**
     * Elimina una promoción y su imagen.
     */
    public function eliminar(int $id): void
    {
        $actual = $this->obtenerModelo($id);
        $this->imagenes->eliminar($actual->url_imagen);
        $this->promocionRepository->delete($actual);
    }

    /**
     * Devuelve la promoción si existe.
     */
    public function buscar(int $id): ?PromocionGestionDatos
    {
        $promocion = $this->promocionRepository->findById($id);

        return $promocion === null ? null : PromocionGestionDatos::fromModel($promocion);
    }

    /**
     * Recorre todas las promociones para el listado del modal.
     *
     * @return list<PromocionGestionDatos>
     */
    public function listar(): array
    {
        return $this->promocionRepository
            ->todas()
            ->map(fn (Promocion $promocion): PromocionGestionDatos => PromocionGestionDatos::fromModel($promocion))
            ->values()
            ->all();
    }

    /**
     * Carga el modelo o lanza si no existe.
     */
    private function obtenerModelo(int $id): Promocion
    {
        $promocion = $this->promocionRepository->findById($id);

        if ($promocion === null) {
            throw new PromocionNoEncontradaException;
        }

        return $promocion;
    }
}
