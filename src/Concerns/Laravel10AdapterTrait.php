<?php

namespace Cosmastech\LaravelStatsDAdapter\Concerns;

use Cosmastech\StatsDClientAdapter\Adapters\Datadog\DatadogStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\League\LeagueStatsDClientAdapter;
use Illuminate\Contracts\Container\BindingResolutionException;
use League\StatsD\Exception\ConfigurationException;

/**
 * This trait serves as a bridge between Laravel 11's MultipleInstanceManager and Laravel 10's version of the class.
 * - Unique driver keys were added in Laravel 11. Prior to that, the config key was required to be named "driver"
 * - Method names for creating the adapter were not studly cased.
 */
trait Laravel10AdapterTrait
{
    /**
     * @param  array<string, mixed>  $config
     * @return InMemoryClientAdapter
     */
    protected function createMemoryDriver(array $config): InMemoryClientAdapter
    {
        return $this->createMemoryAdapter($config);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return DatadogStatsDClientAdapter
     *
     * @throws BindingResolutionException
     */
    protected function createLog_datadogDriver(array $config): DatadogStatsDClientAdapter
    {
        return $this->createLogDatadogAdapter($config);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return DatadogStatsDClientAdapter
     *
     * @throws BindingResolutionException
     */
    protected function createLog_datadogAdapter(array $config): DatadogStatsDClientAdapter
    {
        return $this->createLogDatadogAdapter($config);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return DatadogStatsDClientAdapter
     */
    protected function createDatadogDriver(array $config): DatadogStatsDClientAdapter
    {
        return $this->createDatadogAdapter($config);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return LeagueStatsDClientAdapter
     *
     * @throws ConfigurationException
     */
    protected function createLeagueDriver(array $config): LeagueStatsDClientAdapter
    {
        return $this->createLeagueAdapter($config);
    }

    /**
     * @param  array<string, mixed>|null  $config
     * @return array<string, mixed>|null
     */
    protected function setDriverKeyInConfig(?array $config): ?array
    {
        if (empty($config)) {
            return $config;
        }

        if (array_key_exists("driver", $config)) {
            return $config;
        }

        $config["driver"] = $config["adapter"] ?? null;

        return $config;
    }
}
