<?php

namespace Database\Seeders;

use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\GrupoMesaRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Enums\TipoMesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    /**
     * Inyecta los repositorios de código QR, mesa y grupo.
     */
    public function __construct(
        private readonly CodigoQrRepositoryInterface $codigoQrRepository,
        private readonly MesaRepositoryInterface $mesaRepository,
        private readonly GrupoMesaRepositoryInterface $grupoMesaRepository,
    ) {}

    /**
     * Crea mesas de ejemplo con su código QR asociado.
     */
    public function run(): void
    {
        $tipos = TipoMesa::valoresPersistibles();

        for ($numero = 1; $numero <= 12; $numero++) {
            $tipo = $tipos[($numero - 1) % count($tipos)];
            $mesa = $this->mesaRepository->findByNumero($numero);

            if ($mesa !== null) {
                $this->mesaRepository->update($mesa, ['tipo' => $tipo]);

                continue;
            }

            $codigoQr = $this->codigoQrRepository->findByNumero($numero);

            if ($codigoQr === null) {
                $codigoQr = $this->codigoQrRepository->create([
                    'numero_qr' => $numero,
                    'estado' => 'activo',
                    'codigo_qr' => 'OLIMPIA-MESA-'.str_pad((string) $numero, 2, '0', STR_PAD_LEFT),
                ]);
            }

            $this->mesaRepository->create([
                'numero_mesa' => $numero,
                'tipo' => $tipo,
                'estado' => 'disponible',
                'id_qr' => $codigoQr->id_qr,
            ]);
        }

        $this->unirEjemplo();
    }

    /**
     * Deja un grupo de ejemplo con las mesas 2 y 4.
     */
    private function unirEjemplo(): void
    {
        $primera = $this->mesaRepository->findByNumero(2);
        $segunda = $this->mesaRepository->findByNumero(4);

        if ($primera === null || $segunda === null || $primera->id_grupo !== null || $segunda->id_grupo !== null) {
            return;
        }

        $grupo = $this->grupoMesaRepository->create(['estado' => 'activo']);
        $this->mesaRepository->asignarGrupo(
            [(int) $primera->id_mesa, (int) $segunda->id_mesa],
            (int) $grupo->id_grupo,
        );
    }
}
