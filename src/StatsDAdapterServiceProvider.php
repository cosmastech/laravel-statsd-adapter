<?php

namespace Cosmastech\LaravelStatsDAdapter;

use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class StatsDAdapterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->singleton(
            AdapterManager::class,
            fn ($app) => new AdapterManager($app)
        );

        $this->app->singleton(
            StatsDClientAdapter::class,
            fn ($app) => $app->make(AdapterManager::class)->instance()
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
