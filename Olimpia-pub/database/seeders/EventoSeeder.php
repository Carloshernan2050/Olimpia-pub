<?php

namespace Database\Seeders;

use App\Contracts\Repositories\EventoRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Enums\EstadoEvento;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    /**
     * Inyecta los repositorios de evento y usuario.
     */
    public function __construct(
        private readonly EventoRepositoryInterface $eventoRepository,
        private readonly UsuarioRepositoryInterface $usuarioRepository,
    ) {}

    /**
     * Crea eventos de ejemplo si el catálogo está vacío.
     */
    public function run(): void
    {
        if ($this->eventoRepository->listar()->isNotEmpty()) {
            return;
        }

        $usuario = $this->usuarioRepository->findByCorreo('admin@olimpia.com')
            ?? $this->usuarioRepository->findByCorreo('cliente@olimpia.com');

        if ($usuario === null) {
            return;
        }

        foreach ($this->eventos() as $evento) {
            $this->eventoRepository->create([
                ...$evento,
                'estado' => EstadoEvento::Programado,
                'id_usuario' => $usuario->id_usuario,
            ]);
        }
    }

    /**
     * @return list<array{nombre: string, descripcion: string, fecha: string, hora: string}>
     */
    private function eventos(): array
    {
        return [
            [
                'nombre' => 'Noche de karaoke',
                'descripcion' => 'Micrófono abierto y carta especial',
                'fecha' => now()->addDays(2)->toDateString(),
                'hora' => '21:00',
            ],
            [
                'nombre' => 'Clásico en pantallas',
                'descripcion' => 'Transmisión del partido en todas las TVs',
                'fecha' => now()->addDays(4)->toDateString(),
                'hora' => '18:30',
            ],
            [
                'nombre' => 'Happy hour',
                'descripcion' => '2x1 en cervezas de barril',
                'fecha' => now()->addDays(5)->toDateString(),
                'hora' => '17:00',
            ],
            [
                'nombre' => 'Trivia hincha',
                'descripcion' => 'Preguntas de fútbol y premios',
                'fecha' => now()->addDays(8)->toDateString(),
                'hora' => '20:00',
            ],
            [
                'nombre' => 'DJ en vivo',
                'descripcion' => 'Sesión electrónica hasta cierre',
                'fecha' => now()->addDays(10)->toDateString(),
                'hora' => '22:00',
            ],
            [
                'nombre' => 'Cata de cervezas',
                'descripcion' => 'Seis estilos con guía del bartender',
                'fecha' => now()->addDays(12)->toDateString(),
                'hora' => '19:00',
            ],
        ];
    }
}
