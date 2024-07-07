<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests;

use Cosmastech\LaravelStatsDAdapter\AdapterManager;
use Cosmastech\LaravelStatsDAdapter\Stats;
use Cosmastech\StatsDClientAdapter\Adapters\Datadog\DatadogStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use Cosmastech\StatsDClientAdapter\Clients\Datadog\DatadogLoggingClient;
use DateTimeImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;

class StatsTest extends AbstractTestCase
{
    #[Test]
    public function getDefaultInstance_returnsConfigDefaultAdapter()
    {
        // Given
        Config::set("statsd-adapter.default", "this-does-not-exist-but-thats-ok");

        // When
        $defaultInstanceString = Stats::getDefaultInstance();

        // Then
        self::assertEquals("this-does-not-exist-but-thats-ok", $defaultInstanceString);
    }

    #[Test]
    public function setDefaultInstance_overridesConfigDefault()
    {
        // Given application is configured

        // When
        Stats::setDefaultInstance("yooooo");

        // Then
        self::assertEquals("yooooo", Stats::getDefaultInstance());
    }

    #[Test]
    public function getFacadeRoot_returnsAdapterManager()
    {
        // Given facade has been booted

        // When
        $facadeRoot = Stats::getFacadeRoot();

        // Then
        self::assertInstanceOf(AdapterManager::class, $facadeRoot);
    }

    #[Test]
    public function memoryAdapter_instance_returnsInMemoryClientAdapter()
    {
        // Given
        Config::set("statsd-adapter.default", "memory");

        // When
        /** @var InMemoryClientAdapter $inMemoryClientAdapter */
        $inMemoryClientAdapter = Stats::instance();

        // Then
        self::assertInstanceOf(InMemoryClientAdapter::class, $inMemoryClientAdapter);
    }

    #[Test]
    public function memoryAdapter_logsRespectCarbonTestTime()
    {
        // Given
        /** @var InMemoryClientAdapter $inMemoryClientAdapter */
        $inMemoryClientAdapter = Stats::instance("memory");

        // And
        Carbon::setTestNow("2021-01-01 00:00:00");

        // When
        $inMemoryClientAdapter->increment("some-stat");

        // Then
        $stats = $inMemoryClientAdapter->getStats();
        self::assertEqualsWithDelta(
            (new DateTimeImmutable("2021-01-01 00:00:00"))->getTimestamp(),
            $stats->count[0]->recordedAt->getTimestamp(),
            1
        );
    }

    #[Test]
    public function logDatadog_instance_returnsConfiguredDatadogClient()
    {
        // Given config set up with log_datadog channel

        // When
        $datadogClientAdapter = Stats::instance("log_datadog");

        // Then
        self::assertInstanceOf(DatadogStatsDClientAdapter::class, $datadogClientAdapter);
        self::assertInstanceOf(DatadogLoggingClient::class, $datadogClientAdapter->getClient());
    }
}
