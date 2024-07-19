<?php

namespace Cosmastech\LaravelStatsDAdapter;

use Cosmastech\StatsDClientAdapter\Adapters\Contracts\StatsClientAwareInterface;
use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class StatsClientAwareServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->afterResolving(
            StatsClientAwareInterface::class,
            function (StatsClientAwareInterface $wantsStatsClient, Application $application): void {
                $wantsStatsClient->setStatsClient($application->make(StatsDClientAdapter::class));
            }
        );
    }
}
