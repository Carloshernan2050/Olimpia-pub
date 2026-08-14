<?php

namespace App\Services;

use App\Contracts\Services\DatabaseInstallerInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;
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
            throw new RuntimeException("No existe la conexión de base de datos [{$connection}].");
        }

        return match ($config['driver'] ?? null) {
            'sqlite' => $this->ensureSqliteDatabase($config),
            'mysql', 'mariadb' => $this->ensureMySqlDatabase($connection, $config),
            default => throw new RuntimeException(
                'Solo se soporta crear automáticamente bases sqlite, mysql o mariadb.'
            ),
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
            throw new RuntimeException("No se pudo crear el directorio de SQLite: {$directory}");
        }

        if (touch($database) === false) {
            throw new RuntimeException("No se pudo crear el archivo SQLite: {$database}");
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
        DB::purge($connection);

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
            throw new RuntimeException(
                'No se pudo verificar/crear la base de datos. Revisa que MySQL esté encendido y las credenciales del .env.',
                previous: $exception
            );
        } finally {
            Config::set("database.connections.{$connection}", $config);
            DB::purge($connection);
        }
    }

    /**
     * Comprueba que el nombre de la base de datos solo contenga caracteres permitidos.
     */
    private function assertSafeDatabaseName(string $database): void
    {
        if ($database === '' || preg_match('/^[A-Za-z0-9_]+$/', $database) !== 1) {
            throw new InvalidArgumentException(
                'El nombre de la base de datos solo puede contener letras, números y guion bajo.'
            );
        }
    }
}
