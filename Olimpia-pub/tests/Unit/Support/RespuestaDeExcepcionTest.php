<?php

namespace Tests\Unit\Support;

use App\Exceptions\Promocion\PromocionNoEncontradaException;
use App\Support\Http\RespuestaDeExcepcion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class RespuestaDeExcepcionTest extends TestCase
{
    public function test_responde_json_si_la_peticion_lo_pide(): void
    {
        $request = Request::create('/promociones', 'GET');
        $request->headers->set('Accept', 'application/json');

        $respuesta = RespuestaDeExcepcion::jsonOAviso(
            $request,
            new PromocionNoEncontradaException,
            404,
            redirect()->route('promociones'),
        );

        $this->assertInstanceOf(JsonResponse::class, $respuesta);
        $this->assertSame(404, $respuesta->getStatusCode());
        $this->assertSame('La promoción no existe.', $respuesta->getData(true)['mensaje']);
    }

    public function test_responde_con_aviso_flash_en_la_web(): void
    {
        $respuesta = RespuestaDeExcepcion::jsonOAviso(
            Request::create('/promociones', 'GET'),
            new PromocionNoEncontradaException,
            404,
            redirect()->route('promociones'),
        );

        $this->assertInstanceOf(RedirectResponse::class, $respuesta);
        $this->assertSame('La promoción no existe.', $respuesta->getSession()->get('error'));
    }
}
