<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Prepara cada prueba sin exigir el manifiesto de Vite.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
