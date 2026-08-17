<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\DTOs\Autenticacion\NombrePersona;
use App\DTOs\Autenticacion\RegistrarUsuarioDatos;
use App\Exceptions\Autenticacion\CorreoYaRegistradoException;
use App\Exceptions\Autenticacion\CredencialesInvalidasException;
use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Exceptions\Autenticacion\UsuarioInactivoException;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\AutenticacionService;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Hashing\Hasher;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AutenticacionServiceTest extends TestCase
{
    private UsuarioRepositoryInterface&MockInterface $usuarioRepository;

    private RolRepositoryInterface&MockInterface $rolRepository;

    private StatefulGuard&MockInterface $guard;

    private Hasher&MockInterface $hasher;

    private AutenticacionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuarioRepository = Mockery::mock(UsuarioRepositoryInterface::class);
        $this->rolRepository = Mockery::mock(RolRepositoryInterface::class);
        $this->guard = Mockery::mock(StatefulGuard::class);
        $this->hasher = Mockery::mock(Hasher::class);

        $this->service = new AutenticacionService(
            $this->usuarioRepository,
            $this->rolRepository,
            $this->guard,
            $this->hasher,
        );
    }

    public function test_registrar_crea_usuario_e_inicia_sesion(): void
    {
        $datos = $this->datosRegistro();

        $rol = new Rol(['nombre_rol' => 'cliente']);
        $rol->id_rol = 1;

        $usuario = $this->usuarioConRol();

        $this->usuarioRepository->shouldReceive('findByCorreo')->once()->with('ana@olimpia.com')->andReturn(null);
        $this->rolRepository->shouldReceive('findByNombre')->once()->with('cliente')->andReturn($rol);
        $this->usuarioRepository->shouldReceive('create')->once()->andReturn($usuario);
        $this->guard->shouldReceive('login')->once()->with($usuario);

        $resultado = $this->service->registrar($datos);

        $this->assertSame('ana@olimpia.com', $resultado->correo);
        $this->assertSame('cliente', $resultado->rol);
    }

    public function test_registrar_falla_si_el_correo_ya_existe(): void
    {
        $datos = $this->datosRegistro();

        $this->usuarioRepository->shouldReceive('findByCorreo')->once()->andReturn(new Usuario);
        $this->expectException(CorreoYaRegistradoException::class);

        $this->service->registrar($datos);
    }

    public function test_registrar_falla_si_falta_el_rol_cliente(): void
    {
        $datos = $this->datosRegistro();

        $this->usuarioRepository->shouldReceive('findByCorreo')->once()->andReturn(null);
        $this->rolRepository->shouldReceive('findByNombre')->once()->andReturn(null);
        $this->expectException(RolNoConfiguradoException::class);

        $this->service->registrar($datos);
    }

    public function test_iniciar_sesion_con_credenciales_validas(): void
    {
        $usuario = $this->usuarioConRol();

        $this->usuarioRepository->shouldReceive('findByCorreo')->once()->with('ana@olimpia.com')->andReturn($usuario);
        $this->hasher->shouldReceive('check')->once()->andReturn(true);
        $this->guard->shouldReceive('login')->once()->with($usuario);

        $resultado = $this->service->iniciarSesion('ana@olimpia.com', 'password1');

        $this->assertSame('Ana', $resultado->nombre->primerNombre);
    }

    public function test_iniciar_sesion_falla_si_el_usuario_no_existe(): void
    {
        $this->usuarioRepository->shouldReceive('findByCorreo')->once()->andReturn(null);
        $this->hasher->shouldReceive('check')->once()->andReturn(true);
        $this->expectException(CredencialesInvalidasException::class);

        $this->service->iniciarSesion('nadie@olimpia.com', 'password1');
    }

    public function test_iniciar_sesion_falla_si_la_contrasena_es_incorrecta(): void
    {
        $usuario = $this->usuarioConRol();

        $this->usuarioRepository->shouldReceive('findByCorreo')->once()->andReturn($usuario);
        $this->hasher->shouldReceive('check')->once()->andReturn(false);
        $this->expectException(CredencialesInvalidasException::class);

        $this->service->iniciarSesion('ana@olimpia.com', 'mala');
    }

    public function test_iniciar_sesion_falla_si_el_usuario_esta_inactivo(): void
    {
        $usuario = $this->usuarioConRol();
        $usuario->estado = 'inactivo';

        $this->usuarioRepository->shouldReceive('findByCorreo')->once()->andReturn($usuario);
        $this->hasher->shouldReceive('check')->once()->andReturn(true);
        $this->expectException(UsuarioInactivoException::class);

        $this->service->iniciarSesion('ana@olimpia.com', 'password1');
    }

    public function test_cerrar_sesion_delega_en_el_guard(): void
    {
        $this->guard->shouldReceive('logout')->once();

        $this->service->cerrarSesion();

        $this->guard->shouldHaveReceived('logout');
    }

    private function usuarioConRol(): Usuario
    {
        $rol = new Rol(['nombre_rol' => 'cliente']);
        $rol->id_rol = 1;

        $usuario = new Usuario([
            'primer_nombre' => 'Ana',
            'segundo_nombre' => null,
            'primer_apellido' => 'Perez',
            'segundo_apellido' => null,
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'hash',
            'estado' => 'activo',
            'id_rol' => 1,
        ]);
        $usuario->id_usuario = 10;
        $usuario->setRelation('rol', $rol);

        return $usuario;
    }

    private function datosRegistro(): RegistrarUsuarioDatos
    {
        return new RegistrarUsuarioDatos(
            new NombrePersona('Ana', null, 'Perez', null),
            'ana@olimpia.com',
            'password1'
        );
    }
}
