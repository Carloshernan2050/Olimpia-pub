<?php

namespace App\Services;

use App\Contracts\Services\DatabaseInstallerInterface;
use App\Exceptions\BaseDatos\ArchivoSqliteNoCreadoException;
use App\Exceptions\BaseDatos\BaseDatosNoCreadaException;
use App\Exceptions\BaseDatos\ConexionNoEncontradaException;
use App\Exceptions\BaseDatos\DirectorioSqliteNoCreadoException;
use App\Exceptions\BaseDatos\DriverNoSoportadoException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class DatabaseInstaller implements DatabaseInstallerInterface
{
    /**
     * Garantiza que la base de datos configurada exista y la crea si hace falta.
     *
     * @return bool Verdadero si se creó la base de datos, falso si ya existía.
     */
    public function ensureExists(): bool
    {
        $connection = (string) Config::get('database.default');
        $config = Config::get("database.connections.{$connection}");

        if (! is_array($config)) {
            throw new ConexionNoEncontradaException($connection);
        }

        return match ($config['driver'] ?? null) {
            'sqlite' => $this->ensureSqliteDatabase($config),
            'mysql', 'mariadb' => $this->ensureMySqlDatabase($connection, $config),
            default => throw new DriverNoSoportadoException,
        };
    }

    /**
     * Crea el archivo SQLite y su directorio cuando aún no existen.
     *
     * @param  array<string, mixed>  $config
     */
    private function ensureSqliteDatabase(array $config): bool
    {
        $database = (string) ($config['database'] ?? '');

        if ($database === '' || $database === ':memory:') {
            return false;
        }

        if (file_exists($database)) {
            return false;
        }

        $directory = dirname($database);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new DirectorioSqliteNoCreadoException($directory);
        }

        if (touch($database) === false) {
            throw new ArchivoSqliteNoCreadoException($database);
        }

        return true;
    }

    /**
     * Verifica o crea la base de datos MySQL usando la conexión del servidor.
     *
     * @param  array<string, mixed>  $config
     */
    private function ensureMySqlDatabase(string $connection, array $config): bool
    {
        $database = (string) ($config['database'] ?? '');
        $this->assertSafeDatabaseName($database);

        $serverConfig = $config;
        $serverConfig['database'] = null;

        Config::set("database.connections.{$connection}", $serverConfig);
        $this->purgarConexion($connection);

        try {
            $exists = DB::connection($connection)->select(
                'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
                [$database]
            );

            if ($exists !== []) {
                return false;
            }

            DB::connection($connection)->statement(
                "CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );

            return true;
        } catch (Throwable $exception) {
            throw new BaseDatosNoCreadaException(previous: $exception);
        } finally {
            Config::set("database.connections.{$connection}", $config);
            $this->purgarConexion($connection);
        }
    }

    /**
     * Descarta la conexión cacheada para volver a leer la configuración.
     */
    private function purgarConexion(string $connection): void
    {
        DB::purge($connection);
    }

    /**
     * Comprueba que el nombre de la base de datos solo contenga caracteres permitidos.
     */
    private function assertSafeDatabaseName(string $database): void
    {
        if ($database === '' || preg_match('/^\w+$/', $database) !== 1) {
            throw new InvalidArgumentException(
                'El nombre de la base de datos solo puede contener letras, números y guion bajo.'
            );
        }
    }
}
