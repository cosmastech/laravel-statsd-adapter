<?php

namespace Cosmastech\LaravelStatsDAdapter;

use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class StatsDAdapterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(
            AdapterManager::class,
            fn (Application $app) => new AdapterManager($app),
        );

        $this->app->bind(
            StatsDClientAdapter::class,
            fn (Application $app) => $app->make(AdapterManager::class)->instance()
        );
    }

    /**
     * @return array<int, string|class-string>
     */
    public function provides(): array
    {
        return [AdapterManager::class, StatsDClientAdapter::class];
    }
}
