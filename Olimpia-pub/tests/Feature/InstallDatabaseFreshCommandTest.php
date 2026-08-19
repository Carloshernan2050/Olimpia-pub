<?php

namespace Tests\Feature;

use App\Contracts\Services\DatabaseInstallerInterface;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Tests\TestCase;

class InstallDatabaseFreshCommandTest extends TestCase
{
    public function test_usa_migrate_fresh_cuando_se_indica(): void
    {
        $this->mock(DatabaseInstallerInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('ensureExists')->once()->andReturn(false);
        });

        $this->artisan('db:install', ['--fresh' => true])->assertSuccessful();
        $this->assertTrue(Schema::hasTable('usuario'));
    }
}
