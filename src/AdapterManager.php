<?php

namespace Cosmastech\LaravelStatsDAdapter;

use Carbon\FactoryImmutable;
use Carbon\WrapperClock;
use Cosmastech\StatsDClientAdapter\Adapters\Datadog\DatadogStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\League\LeagueStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Clients\Datadog\DatadogLoggingClient;
use DataDog\DogStatsd;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\MultipleInstanceManager;
use League\StatsD\Exception\ConfigurationException;
use Psr\Clock\ClockInterface;

/**
 * @property array<int, StatsDClientAdapter> $instances
 * @method StatsDClientAdapter instance($name = null)
 *
 * @mixin  \Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter
 */
class AdapterManager extends MultipleInstanceManager
{
    protected $driverKey = 'adapter';

    protected string $defaultInstanceName;

    /**
     * The configuration repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var array<mixed, mixed>
     */
    protected array $defaultTags;

    /**
     * @inheritDoc
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->config = $this->app->get('config');
    }

    /**
     * Get instance of StatsDClientAdapter by channel name.
     *
     * @param  string|null  $name
     * @return StatsDClientAdapter
     */
    public function channel(string $name = null): StatsDClientAdapter
    {
        return $this->instance($name);
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
     * @param  array<mixed, mixed>  $tags
     * @return void
     */
    public function setDefaultTags(array $tags): void
    {
        $this->defaultTags = $tags;

        foreach ($this->instances as $instance) {
            $instance->setDefaultTags($this->defaultTags);
        }
    }

    /**
     * @return array<mixed, mixed>
     */
    public function getDefaultTags(): array
    {
        return $this->defaultTags ?? $this->getDefaultTagsFromConfig();
    }

    /**
     * @return array<mixed, mixed>
     */
    protected function getDefaultTagsFromConfig(): array
    {
        return $this->config->get('statsd-adapter.default_tags', []);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return InMemoryClientAdapter
     */
    protected function createMemoryAdapter(array $config): InMemoryClientAdapter
    {
        return new InMemoryClientAdapter(
            $this->getDefaultTags(),
            clock: $this->getClockImplementation()
        );
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
     *
     * @throws BindingResolutionException
     */
    protected function createLogDatadogAdapter(array $config): DatadogStatsDClientAdapter
    {
        $logLevel = $config['log_level'] ?? 'debug';

        return new DatadogStatsDClientAdapter(
            new DatadogLoggingClient($this->app->make('log'), $config, $logLevel),
            $this->getDefaultTags(),
            clock: $this->getClockImplementation()
        );
    }

    /**
     * @param  array<string, mixed>  $config
     * @return DatadogStatsDClientAdapter
     */
    protected function createDatadogAdapter(array $config): DatadogStatsDClientAdapter
    {
        return new DatadogStatsDClientAdapter(
            new DogStatsd($config),
            $this->getDefaultTags(),
            clock: $this->getClockImplementation()
        );
    }

    /**
     * @param  array<string, mixed>  $config
     * @return LeagueStatsDClientAdapter
     *
     * @throws ConfigurationException
     */
    protected function createLeagueAdapter(array $config): LeagueStatsDClientAdapter
    {
        return LeagueStatsDClientAdapter::fromConfig(
            $config,
            $this->getDefaultTags(),
            clock: $this->getClockImplementation()
        );
    }

    protected function getClockImplementation(): ClockInterface
    {
        return new WrapperClock(FactoryImmutable::getDefaultInstance());
    }
}
