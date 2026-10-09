<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $cachedConfig = __DIR__ . '/../bootstrap/cache/config.php';
        if (file_exists($cachedConfig)) {
            @unlink($cachedConfig);
        }

        $app = parent::createApplication();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        return $app;
    }
}
