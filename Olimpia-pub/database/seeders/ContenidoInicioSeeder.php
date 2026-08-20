<?php

namespace Database\Seeders;

use App\Contracts\Repositories\ContenidoInicioRepositoryInterface;
use App\Enums\PosicionInicio;
use App\Enums\TipoBloqueInicio;
use Illuminate\Database\Seeder;

class ContenidoInicioSeeder extends Seeder
{
    /**
     * Inyecta el repositorio de bloques de Home.
     */
    public function __construct(
        private readonly ContenidoInicioRepositoryInterface $contenidoInicioRepository,
    ) {}

    /**
     * Crea los textos, el video y las imágenes de la portada.
     */
    public function run(): void
    {
        $bloques = [
            [
                'posicion' => PosicionInicio::SuperiorIzquierda,
                'tipo' => TipoBloqueInicio::Texto,
                'titulo' => 'El partido se vive mejor en Olimpia',
                'cuerpo' => 'Pantallas, ambiente y una carta pensada para quedarte hasta el último minuto. Bienvenido al pub donde el deporte manda.',
                'url_media' => null,
                'orden' => 1,
            ],
            [
                'posicion' => PosicionInicio::SuperiorCentro,
                'tipo' => TipoBloqueInicio::Video,
                'titulo' => 'Ambiente Olimpia',
                'cuerpo' => null,
                'url_media' => '/media/inicio/portada.mp4',
                'orden' => 2,
            ],
            [
                'posicion' => PosicionInicio::SuperiorDerecha,
                'tipo' => TipoBloqueInicio::Texto,
                'titulo' => 'Hoy en barra',
                'cuerpo' => 'Promos de jarras, tablas para compartir y la retransmisión del partido en todas las pantallas. Pregunta por la mesa con mejor vista.',
                'url_media' => null,
                'orden' => 3,
            ],
            [
                'posicion' => PosicionInicio::InferiorIzquierda,
                'tipo' => TipoBloqueInicio::Imagen,
                'titulo' => 'Zona de pantallas',
                'cuerpo' => null,
                'url_media' => '/media/inicio/zona-pantallas.svg',
                'orden' => 4,
            ],
            [
                'posicion' => PosicionInicio::InferiorCentro,
                'tipo' => TipoBloqueInicio::Texto,
                'titulo' => 'Carta de temporada',
                'cuerpo' => 'Hamburguesas, alitas y limonada natural para la previa. Pide desde tu mesa o en barra y sigue el marcador sin perderte nada.',
                'url_media' => null,
                'orden' => 5,
            ],
            [
                'posicion' => PosicionInicio::InferiorDerecha,
                'tipo' => TipoBloqueInicio::Imagen,
                'titulo' => 'Terraza Olimpia',
                'cuerpo' => null,
                'url_media' => '/media/inicio/terraza.svg',
                'orden' => 6,
            ],
        ];

        foreach ($bloques as $bloque) {
            if ($this->contenidoInicioRepository->findByPosicion($bloque['posicion']->value) !== null) {
                continue;
            }

            $this->contenidoInicioRepository->create([
                'posicion' => $bloque['posicion']->value,
                'tipo' => $bloque['tipo']->value,
                'titulo' => $bloque['titulo'],
                'cuerpo' => $bloque['cuerpo'],
                'url_media' => $bloque['url_media'],
                'orden' => $bloque['orden'],
                'estado' => 'activo',
            ]);
        }
    }
}
