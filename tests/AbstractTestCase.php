<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests;

use Cosmastech\LaravelStatsDAdapter\AdapterManager;
use Cosmastech\LaravelStatsDAdapter\StatsClientAwareServiceProvider;
use Illuminate\Config\Repository;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class AbstractTestCase extends TestCase
{
    use WithWorkbench;

    protected $enablesPackageDiscoveries = true;

    protected function getPackageProviders($app)
    {
        return [StatsClientAwareServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        /** @var Repository $config */
        $config = $app->make('config');

        $config->set('statsd-adapter', include __DIR__ . '/../config/statsd-adapter.php');
    }

    protected function createAdapterManager(): AdapterManager
    {
        return new AdapterManager($this->app);
    }
}
