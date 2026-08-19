<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Autenticacion\RegistrarUsuarioDatos;
use App\DTOs\Autenticacion\UsuarioAutenticadoDatos;
use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Models\Rol;
use App\Models\Usuario;
use Tests\TestCase;

class AutenticacionDatosTest extends TestCase
{
    public function test_from_validated_normaliza_nombres_vacios_a_nulo(): void
    {
        $datos = RegistrarUsuarioDatos::fromValidated([
            'primer_nombre' => 'Ana',
            'segundo_nombre' => '   ',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => null,
            'correo' => 'ana@olimpia.com',
            'contrasena' => 'password1',
        ]);

        $this->assertNull($datos->nombre->segundoNombre);
        $this->assertNull($datos->nombre->segundoApellido);
        $this->assertSame('ana@olimpia.com', $datos->toPersistence()['correo']);
    }

    public function test_usuario_autenticado_falla_sin_rol(): void
    {
        $usuario = new Usuario([
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => 'ana@olimpia.com',
            'estado' => 'activo',
        ]);
        $usuario->id_usuario = 1;
        $usuario->setRelation('rol', null);

        $this->expectException(RolNoConfiguradoException::class);

        UsuarioAutenticadoDatos::fromModel($usuario);
    }

    public function test_usuario_autenticado_expone_datos_publicos(): void
    {
        $rol = new Rol(['nombre_rol' => 'cliente']);
        $rol->id_rol = 2;

        $usuario = new Usuario([
            'primer_nombre' => 'Ana',
            'segundo_nombre' => 'Maria',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Lopez',
            'correo' => 'ana@olimpia.com',
            'estado' => 'activo',
        ]);
        $usuario->id_usuario = 10;
        $usuario->setRelation('rol', $rol);

        $datos = UsuarioAutenticadoDatos::fromModel($usuario);

        $this->assertSame([
            'id_usuario' => 10,
            'primer_nombre' => 'Ana',
            'segundo_nombre' => 'Maria',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Lopez',
            'correo' => 'ana@olimpia.com',
            'estado' => 'activo',
            'rol' => 'cliente',
        ], $datos->toArray());
    }
}
