<?php

namespace Tests\Unit\Services;

use App\Exceptions\BaseDatos\ConexionNoEncontradaException;
use App\Exceptions\BaseDatos\DriverNoSoportadoException;
use App\Services\DatabaseInstaller;
use Illuminate\Support\Facades\Config;
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
}
