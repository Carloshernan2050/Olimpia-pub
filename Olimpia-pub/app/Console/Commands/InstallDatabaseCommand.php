<?php

namespace App\Console\Commands;

use App\Contracts\Services\DatabaseInstallerInterface;
use Illuminate\Console\Command;

class InstallDatabaseCommand extends Command
{
    protected $signature = 'db:install
                            {--fresh : Elimina todas las tablas antes de migrar}
                            {--seed : Ejecuta los seeders después de migrar}';

    protected $description = 'Verifica/crea la base de datos, corre migraciones y opcionalmente seeders';

    /**
     * Verifica o crea la base de datos, ejecuta las migraciones y, si se pide, los seeders.
     */
    public function handle(DatabaseInstallerInterface $databaseInstaller): int
    {
        $this->info('Verificando base de datos...');

        $created = $databaseInstaller->ensureExists();

        if ($created) {
            $this->info('Base de datos creada.');
        } else {
            $this->info('La base de datos ya existe.');
        }

        $migrateCommand = $this->option('fresh') ? 'migrate:fresh' : 'migrate';
        $migrateArguments = ['--force' => true];

        if ($this->option('seed')) {
            $migrateArguments['--seed'] = true;
        }

        $exitCode = $this->call($migrateCommand, $migrateArguments);

        if ($exitCode !== self::SUCCESS) {
            return $exitCode;
        }

        $this->info('Instalación de base de datos completada.');

        return self::SUCCESS;
    }
}
