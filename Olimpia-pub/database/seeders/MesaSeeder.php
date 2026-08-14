<?php

namespace Database\Seeders;

use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    /**
     * Inyecta los repositorios de código QR y mesa.
     */
    public function __construct(
        private readonly CodigoQrRepositoryInterface $codigoQrRepository,
        private readonly MesaRepositoryInterface $mesaRepository
    ) {
    }

    /**
     * Crea mesas de ejemplo con su código QR asociado.
     */
    public function run(): void
    {
        for ($numero = 1; $numero <= 4; $numero++) {
            if ($this->mesaRepository->findByNumero($numero) !== null) {
                continue;
            }

            $codigoQr = $this->codigoQrRepository->findByNumero($numero);

            if ($codigoQr === null) {
                $codigoQr = $this->codigoQrRepository->create([
                    'numero_qr' => $numero,
                    'estado' => 'activo',
                    'codigo_qr' => 'OLIMPIA-MESA-' . str_pad((string) $numero, 2, '0', STR_PAD_LEFT),
                ]);
            }

            $this->mesaRepository->create([
                'numero_mesa' => $numero,
                'estado' => 'disponible',
                'id_qr' => $codigoQr->id_qr,
            ]);
        }
    }
}
