<?php

namespace Cosmastech\LaravelStatsDAdapter;

use Carbon\FactoryImmutable;
use Carbon\WrapperClock;
use Cosmastech\StatsDClientAdapter\Adapters\Datadog\DatadogStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\League\LeagueStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Clients\Datadog\DatadogLoggingClient;
use Cosmastech\StatsDClientAdapter\Utility\SampleRateDecider\SampleRateSendDecider;
use DataDog\DogStatsd;
use Illuminate\Support\MultipleInstanceManager;
use League\StatsD\Client;
use League\StatsD\Exception\ConfigurationException;

/**
 * @mixin  \Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter
 */
class AdapterManager extends MultipleInstanceManager
{
    protected $driverKey = 'adapter';

    protected string $defaultInstanceName;

    /**
     * The configuration repository instance.
     * @todo remove this after laravel/framework release 2024-07-06
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @inheritDoc
     */
    public function __construct($app)
    {
        // @todo remove this after laravel/framework release 2024-07-06
        parent::__construct($app);
        $this->config = $this->app->get('config');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultInstance()
    {
        return $this->defaultInstanceName ?? $this->config->get("statsd-adapter.default");
    }

    /**
     * @inheritDoc
     */
    public function setDefaultInstance($name)
    {
        $this->defaultInstanceName = $name;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getInstanceConfig($name)
    {
        return $this->config->get("statsd-adapter.channels.{$name}");
    }

    /**
     * @param  array<string, mixed>  $config
     * @return InMemoryClientAdapter
     */
    protected function createMemoryAdapter(array $config): InMemoryClientAdapter
    {
        $wrapperClock = new WrapperClock(FactoryImmutable::getDefaultInstance());

        return new InMemoryClientAdapter($wrapperClock);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return DatadogStatsDClientAdapter
     */
    protected function createLog_datadogAdapter(array $config): DatadogStatsDClientAdapter
    {
        return $this->createLogDatadogAdapter($config);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return DatadogStatsDClientAdapter
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createLogDatadogAdapter(array $config): DatadogStatsDClientAdapter
    {
        $logLevel = $config['log_level'] ?? 'debug';

        return new DatadogStatsDClientAdapter(
            new DatadogLoggingClient($this->app->make('log'), $logLevel, $config)
        );
    }

    /**
     * @param  array<string, mixed>  $config
     * @return DatadogStatsDClientAdapter
     */
    protected function createDatadogAdapter(array $config): DatadogStatsDClientAdapter
    {
        return new DatadogStatsDClientAdapter(new DogStatsd($config));
    }

    /**
     * @param array<string, mixed> $config
     * @return LeagueStatsDClientAdapter
     * @throws ConfigurationException
     */
    protected function createLeagueAdapter(array $config): LeagueStatsDClientAdapter
    {
        $leagueClient = new Client($config['instance_id'] ?? null);
        $leagueClient->configure($config);

        return new LeagueStatsDClientAdapter($leagueClient, new SampleRateSendDecider());
    }
}
