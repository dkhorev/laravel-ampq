<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use dkhorev\LaravelAmpq\LaravelAmpqServiceProvider;

/**
 * Class TestCase
 *
 * @package       Tests
 * @backupGlobals disabled
 */
abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [LaravelAmpqServiceProvider::class];
    }
}
