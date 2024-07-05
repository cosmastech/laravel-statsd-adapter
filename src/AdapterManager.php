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

class AdapterManager extends MultipleInstanceManager
{
    protected $driverKey = 'adapter';

    /**
     * The configuration repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->config = $this->app->get('config');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultInstance()
    {
        return $this->config->get("statsd-adapter.default");
    }

    /**
     * @inheritDoc
     */
    public function setDefaultInstance($name)
    {
        // not sure if this is needed
    }

    /**
     * @inheritDoc
     */
    public function getInstanceConfig($name)
    {
        return $this->config->get("statsd-adapter.channels.{$name}");
    }


    protected function createMemoryAdapter(array $config): InMemoryClientAdapter
    {
        $wrapperClock = new WrapperClock(FactoryImmutable::getDefaultInstance());

        return new InMemoryClientAdapter($wrapperClock);
    }

    protected function createLog_datadogAdapter(array $config): DatadogStatsDClientAdapter
    {
        $logLevel = $config['log_level'] ?? 'debug';

        return new DatadogStatsDClientAdapter(
            new DatadogLoggingClient($this->app->make('log'), $logLevel, $config)
        );
    }

    protected function createDatadogAdapter(array $config): DatadogStatsDClientAdapter
    {
        return new DatadogStatsDClientAdapter(new DogStatsd($config));
    }

    /**
     * @throws ConfigurationException
     */
    protected function createLeagueAdapter(array $config): LeagueStatsDClientAdapter
    {
        $leagueClient = new Client($config['instance_id'] ?? null);
        $leagueClient->configure($config);

        return new LeagueStatsDClientAdapter($leagueClient, new SampleRateSendDecider());
    }
}
