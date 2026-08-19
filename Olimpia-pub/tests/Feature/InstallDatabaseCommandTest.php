<?php

namespace Tests\Feature;

use App\Contracts\Services\DatabaseInstallerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class InstallDatabaseCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_informa_cuando_la_base_ya_existe(): void
    {
        $this->mock(DatabaseInstallerInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('ensureExists')->once()->andReturn(false);
        });

        $this->artisan('db:install')
            ->expectsOutput('La base de datos ya existe.')
            ->assertSuccessful();
    }

    public function test_informa_cuando_crea_la_base(): void
    {
        $this->mock(DatabaseInstallerInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('ensureExists')->once()->andReturn(true);
        });

        $this->artisan('db:install')
            ->expectsOutput('Base de datos creada.')
            ->assertSuccessful();
    }

    public function test_ejecuta_seeders(): void
    {
        $this->mock(DatabaseInstallerInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('ensureExists')->once()->andReturn(false);
        });

        $this->artisan('db:install', [
            '--seed' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('rol', [
            'nombre_rol' => 'cliente',
        ]);
    }
}
