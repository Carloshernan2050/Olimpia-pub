<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\Autenticacion\CorreoYaRegistradoException;
use App\Exceptions\Autenticacion\CredencialesInvalidasException;
use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Exceptions\Autenticacion\UsuarioInactivoException;
use App\Exceptions\BaseDatos\ArchivoSqliteNoCreadoException;
use App\Exceptions\BaseDatos\BaseDatosNoCreadaException;
use App\Exceptions\BaseDatos\ConexionNoEncontradaException;
use App\Exceptions\BaseDatos\DirectorioSqliteNoCreadoException;
use App\Exceptions\BaseDatos\DriverNoSoportadoException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ExcepcionesDeDominioTest extends TestCase
{
    public function test_excepciones_de_autenticacion_exponen_mensaje_por_defecto(): void
    {
        $this->assertNotSame('', (new CorreoYaRegistradoException)->getMessage());
        $this->assertNotSame('', (new CredencialesInvalidasException)->getMessage());
        $this->assertNotSame('', (new UsuarioInactivoException)->getMessage());
        $this->assertSame(
            'El rol requerido no está configurado.',
            (new RolNoConfiguradoException)->getMessage()
        );
        $this->assertSame(
            'Falta el rol.',
            (new RolNoConfiguradoException('Falta el rol.'))->getMessage()
        );
    }

    public function test_excepciones_de_base_datos_incluyen_contexto(): void
    {
        $this->assertSame(
            'No existe la conexión de base de datos [mysql].',
            (new ConexionNoEncontradaException('mysql'))->getMessage()
        );
        $this->assertSame(
            'No se pudo crear el archivo SQLite: /tmp/db.sqlite',
            (new ArchivoSqliteNoCreadoException('/tmp/db.sqlite'))->getMessage()
        );
        $this->assertSame(
            'No se pudo crear el directorio de SQLite: /tmp/data',
            (new DirectorioSqliteNoCreadoException('/tmp/data'))->getMessage()
        );
        $this->assertNotSame('', (new DriverNoSoportadoException)->getMessage());
    }

    public function test_base_datos_no_creada_conserva_la_causa(): void
    {
        $causa = new RuntimeException('conexion rechazada');
        $excepcion = new BaseDatosNoCreadaException(previous: $causa);

        $this->assertSame($causa, $excepcion->getPrevious());
        $this->assertStringContainsString('MySQL', $excepcion->getMessage());
    }
}
