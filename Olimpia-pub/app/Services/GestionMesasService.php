<?php

namespace App\Services;

use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\PedidoRepositoryInterface;
use App\Contracts\Services\EnlacePedidoMesaInterface;
use App\Contracts\Services\GeneradorCodigoQrInterface;
use App\Contracts\Services\GestionMesasServiceInterface;
use App\DTOs\Dashboard\GuardarMesaDatos;
use App\DTOs\Dashboard\MesaTarjetaDatos;
use App\Exceptions\Mesa\MesaConPedidosException;
use App\Exceptions\Mesa\MesaEnGrupoException;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Exceptions\Mesa\MesaNumeroDuplicadoException;
use App\Models\CodigoQr;
use App\Models\Mesa;
use Illuminate\Database\ConnectionInterface;

class GestionMesasService implements GestionMesasServiceInterface
{
    /**
     * Inyecta mesas, códigos QR y el generador.
     */
    public function __construct(
        private readonly MesaRepositoryInterface $mesaRepository,
        private readonly CodigoQrRepositoryInterface $codigoQrRepository,
        private readonly PedidoRepositoryInterface $pedidoRepository,
        private readonly GeneradorCodigoQrInterface $generadorQr,
        private readonly EnlacePedidoMesaInterface $enlacePedido,
        private readonly ConnectionInterface $conexion,
    ) {}

    /**
     * Crea la mesa y su código en una sola transacción.
     */
    public function crear(GuardarMesaDatos $datos): MesaTarjetaDatos
    {
        $this->asegurarNumeroLibre($datos->numero);

        $mesa = $this->conexion->transaction(
            fn (): Mesa => $this->persistirAlta($datos),
        );

        return $this->tarjeta($mesa);
    }

    /**
     * Actualiza número, tipo y código de la mesa.
     */
    public function actualizar(int $id, GuardarMesaDatos $datos): MesaTarjetaDatos
    {
        $mesa = $this->obtenerModelo($id);
        $this->asegurarFueraDeGrupo($mesa);
        $this->asegurarNumeroLibre($datos->numero, $mesa->id_mesa);

        $actualizada = $this->conexion->transaction(
            fn (): Mesa => $this->persistirCambio($mesa, $datos),
        );

        return $this->tarjeta($actualizada);
    }

    /**
     * Quita la mesa y su QR si no hay pedidos asociados.
     */
    public function eliminar(int $id): void
    {
        $mesa = $this->obtenerModelo($id);
        $this->asegurarFueraDeGrupo($mesa);

        if ($this->pedidoRepository->existenDeMesa((int) $mesa->id_mesa)) {
            throw new MesaConPedidosException;
        }

        $this->conexion->transaction(function () use ($mesa): void {
            $codigo = $mesa->codigoQr;
            $this->mesaRepository->delete($mesa);

            if ($codigo !== null) {
                $this->codigoQrRepository->delete($codigo);
            }
        });
    }

    /**
     * Busca una mesa con su QR listo para el modal.
     */
    public function buscar(int $id): ?MesaTarjetaDatos
    {
        $mesa = $this->mesaRepository->findById($id);

        return $mesa === null ? null : $this->tarjeta($mesa);
    }

    private function persistirAlta(GuardarMesaDatos $datos): Mesa
    {
        $codigo = $this->codigoPara($datos);

        return $this->mesaRepository->create($datos->paraCrear((int) $codigo->id_qr));
    }

    private function persistirCambio(Mesa $mesa, GuardarMesaDatos $datos): Mesa
    {
        $actualizada = $this->mesaRepository->update($mesa, $datos->paraActualizar());
        $codigo = $actualizada->codigoQr;

        if ($codigo !== null) {
            $this->codigoQrRepository->update($codigo, [
                'numero_qr' => $datos->numero,
                'codigo_qr' => $datos->codigoQr(),
            ]);
        }

        return $this->obtenerModelo((int) $actualizada->id_mesa);
    }

    private function codigoPara(GuardarMesaDatos $datos): CodigoQr
    {
        $existente = $this->codigoQrRepository->findByNumero($datos->numero);

        if ($existente === null) {
            return $this->codigoQrRepository->create([
                'numero_qr' => $datos->numero,
                'estado' => 'activo',
                'codigo_qr' => $datos->codigoQr(),
            ]);
        }

        if ($existente->mesa !== null) {
            throw new MesaNumeroDuplicadoException;
        }

        return $this->codigoQrRepository->update($existente, [
            'estado' => 'activo',
            'codigo_qr' => $datos->codigoQr(),
        ]);
    }

    private function asegurarNumeroLibre(int $numero, ?int $excepto = null): void
    {
        $existente = $this->mesaRepository->findByNumero($numero);

        if ($existente !== null && (int) $existente->id_mesa !== $excepto) {
            throw new MesaNumeroDuplicadoException;
        }
    }

    private function asegurarFueraDeGrupo(Mesa $mesa): void
    {
        if ($mesa->id_grupo !== null) {
            throw new MesaEnGrupoException;
        }
    }

    private function obtenerModelo(int $id): Mesa
    {
        $mesa = $this->mesaRepository->findById($id);

        if ($mesa === null) {
            throw new MesaNoEncontradaException;
        }

        return $mesa;
    }

    private function tarjeta(Mesa $mesa): MesaTarjetaDatos
    {
        $codigo = (string) ($mesa->codigoQr?->codigo_qr ?? GuardarMesaDatos::codigoDeNumero((int) $mesa->numero_mesa));

        return MesaTarjetaDatos::fromModel($mesa, $this->generadorQr->svg($this->enlacePedido->url($codigo)));
    }
}
