<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests;

use Cosmastech\StatsDClientAdapter\Adapters\Datadog\DatadogStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\League\LeagueStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Clients\Datadog\DatadogLoggingClient;
use Illuminate\Support\Facades\Config;
use League\StatsD\StatsDClient;
use PHPUnit\Framework\Attributes\Test;

class AdapterManagerTest extends AbstractTestCase
{
    #[Test]
    public function getDefaultInstance_returnsConfigDefaultAdapter(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        // And
        Config::set("statsd-adapter.default", "this-does-not-exist-but-thats-ok");

        // When
        $defaultInstanceString = $adapterManager->getDefaultInstance();

        // Then
        self::assertEquals("this-does-not-exist-but-thats-ok", $defaultInstanceString);
    }

    #[Test]
    public function setDefaultInstance_overridesConfigDefault(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        // When
        $adapterManager->setDefaultInstance("yooooo");

        // Then
        self::assertEquals("yooooo", $adapterManager->getDefaultInstance());
    }

    #[Test]
    public function memoryAdapter_instance_returnsInMemoryClientAdapter(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        // And
        Config::set("statsd-adapter.default", "memory");

        // When
        /** @var InMemoryClientAdapter $inMemoryClientAdapter */
        $inMemoryClientAdapter = $adapterManager->instance();

        // Then
        self::assertInstanceOf(InMemoryClientAdapter::class, $inMemoryClientAdapter);
    }

    #[Test]
    public function logDatadog_instance_returnsConfiguredDatadogClient(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        // And "log_datadog" channel is set and app is booted

        // When
        $datadogClientAdapter = $adapterManager->instance("log_datadog");

        // Then
        self::assertInstanceOf(DatadogStatsDClientAdapter::class, $datadogClientAdapter);
        self::assertInstanceOf(DatadogLoggingClient::class, $datadogClientAdapter->getClient());
    }

    public function league_instance_returnsConfiguredLeagueStatsDClient(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        // And "league" channel is set and app is booted

        // When
        /** @var LeagueStatsDClientAdapter $leagueStatsDClientAdapter */
        $leagueStatsDClientAdapter = $adapterManager->instance("league");

        // Then
        self::assertInstanceOf(LeagueStatsDClientAdapter::class, $leagueStatsDClientAdapter);
        self::assertInstanceOf(StatsDClient::class, $leagueStatsDClientAdapter->getClient());
    }
}
