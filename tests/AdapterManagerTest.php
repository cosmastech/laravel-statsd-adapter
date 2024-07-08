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
    public function instance_memoryAdapter_returnsInMemoryClientAdapter(): void
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
    public function instance_logDatadog_returnsConfiguredDatadogClient(): void
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

    public function instance_league_returnsConfiguredLeagueStatsDClient(): void
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

    #[Test]
    public function getDefaultTags_withNoDefaultTagsSet_returnsEmptyArray(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        // Then
        self::assertEquals([], $adapterManager->getDefaultTags());
    }

    #[Test]
    public function getDefaultTags_withDefaultTagsSet_returnsDefaultTagsArray(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        // When
        $adapterManager->setDefaultTags(['abc' => true]);

        // Then
        self::assertSame(["abc" => true], $adapterManager->getDefaultTags());
    }

    #[Test]
    public function setDefaultTags_passesTagsToExistingInstance(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        /** @var InMemoryClientAdapter $inMemoryClientAdapter */
        $inMemoryClientAdapter = $adapterManager->instance("memory");

        // When
        $adapterManager->setDefaultTags(["abc" => "hello"]);

        // Then
        self::assertSame(["abc" => "hello"], $inMemoryClientAdapter->getDefaultTags());
    }

    #[Test]
    public function setDefaultTags_passesToNewInstance(): void
    {
        // Given
        $adapterManager = $this->createAdapterManager();

        // And an instance is created
        $adapterManager->instance("log_datadog");

        // When
        $adapterManager->setDefaultTags(["abc" => "123"]);

        // Then
        self::assertEquals(["abc" => "123"], $adapterManager->instance("memory")->getDefaultTags());
    }
}
