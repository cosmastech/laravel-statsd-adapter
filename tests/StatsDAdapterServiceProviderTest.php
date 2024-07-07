<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests;

use Cosmastech\StatsDClientAdapter\Adapters\Datadog\DatadogStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Clients\Datadog\DatadogLoggingClient;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;

class StatsDAdapterServiceProviderTest extends AbstractTestCase
{
    #[Test]
    public function makeStatsDClientAdapter_returnsDefaultInstance(): void
    {
        // Given application is booted

        // And
        Config::set("statsd-adapter.default", "log_datadog");

        // When
        $clientAdapter = $this->app->make(StatsDClientAdapter::class);

        // Then
        self::assertInstanceOf(DatadogStatsDClientAdapter::class, $clientAdapter);
        self::assertInstanceOf(DatadogLoggingClient::class, $clientAdapter->getClient());
    }

    #[Test]
    public function makeStatsDClientAdapter_returnsSingleton(): void
    {
        // Given application is booted

        // When
        $clientAdapter = $this->app->make(StatsDClientAdapter::class);

        // Then
        self::assertInstanceOf(InMemoryClientAdapter::class, $clientAdapter);

        // And
        self::assertSame($clientAdapter, $this->app->make(StatsDClientAdapter::class));
    }
}
