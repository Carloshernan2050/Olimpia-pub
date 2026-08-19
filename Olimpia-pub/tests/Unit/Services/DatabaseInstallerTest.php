<?php

namespace Tests\Unit\Services;

use App\Exceptions\BaseDatos\BaseDatosNoCreadaException;
use App\Exceptions\BaseDatos\ConexionNoEncontradaException;
use App\Exceptions\BaseDatos\DriverNoSoportadoException;
use App\Services\DatabaseInstaller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class DatabaseInstallerTest extends TestCase
{
    public function test_sqlite_en_memoria_no_crea_archivo(): void
    {
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $creada = (new DatabaseInstaller)->ensureExists();

        $this->assertFalse($creada);
    }

    public function test_sqlite_vacio_no_crea_archivo(): void
    {
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => '',
        ]);

        $this->assertFalse((new DatabaseInstaller)->ensureExists());
    }

    public function test_sqlite_crea_directorio_y_archivo_si_faltan(): void
    {
        $directorio = sys_get_temp_dir().DIRECTORY_SEPARATOR.'olimpia-sqlite-'.uniqid();
        $archivo = $directorio.DIRECTORY_SEPARATOR.'database.sqlite';

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $archivo,
        ]);

        try {
            $this->assertTrue((new DatabaseInstaller)->ensureExists());
            $this->assertFileExists($archivo);
            $this->assertFalse((new DatabaseInstaller)->ensureExists());
        } finally {
            if (is_file($archivo)) {
                unlink($archivo);
            }
            if (is_dir($directorio)) {
                rmdir($directorio);
            }
        }
    }

    public function test_falla_si_la_conexion_no_existe(): void
    {
        Config::set('database.default', 'fantasma');
        Config::set('database.connections.fantasma', null);

        $this->expectException(ConexionNoEncontradaException::class);

        (new DatabaseInstaller)->ensureExists();
    }

    public function test_falla_si_el_driver_no_esta_soportado(): void
    {
        Config::set('database.default', 'pgsql');
        Config::set('database.connections.pgsql', [
            'driver' => 'pgsql',
            'database' => 'olimpia',
        ]);

        $this->expectException(DriverNoSoportadoException::class);

        (new DatabaseInstaller)->ensureExists();
    }

    public function test_mysql_rechaza_nombre_de_base_invalido(): void
    {
        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql', [
            'driver' => 'mysql',
            'database' => 'olimpia-pub',
        ]);

        $this->expectException(InvalidArgumentException::class);

        (new DatabaseInstaller)->ensureExists();
    }

    public function test_mysql_no_crea_si_la_base_ya_existe(): void
    {
        $this->configurarMysql();

        $conexion = Mockery::mock();
        $conexion->shouldReceive('select')->once()->andReturn([(object) ['SCHEMA_NAME' => 'olimpia']]);

        DB::shouldReceive('purge')->twice();
        DB::shouldReceive('connection')->andReturn($conexion);

        $this->assertFalse((new DatabaseInstaller)->ensureExists());
    }

    public function test_mysql_crea_la_base_si_no_existe(): void
    {
        $this->configurarMysql();

        $conexion = Mockery::mock();
        $conexion->shouldReceive('select')->once()->andReturn([]);
        $conexion->shouldReceive('statement')->once()->andReturn(true);

        DB::shouldReceive('purge')->twice();
        DB::shouldReceive('connection')->andReturn($conexion);

        $this->assertTrue((new DatabaseInstaller)->ensureExists());
    }

    public function test_mysql_envuelve_errores_de_conexion(): void
    {
        $this->configurarMysql();

        $conexion = Mockery::mock();
        $conexion->shouldReceive('select')->once()->andThrow(new RuntimeException('servidor caido'));

        DB::shouldReceive('purge')->twice();
        DB::shouldReceive('connection')->andReturn($conexion);

        $this->expectException(BaseDatosNoCreadaException::class);

        (new DatabaseInstaller)->ensureExists();
    }

    /**
     * @return void
     */
    private function configurarMysql(): void
    {
        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql', [
            'driver' => 'mysql',
            'database' => 'olimpia',
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
        ]);
    }
}
