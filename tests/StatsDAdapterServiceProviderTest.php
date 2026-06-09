<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests;

use Cosmastech\StatsDClientAdapter\Adapters\Datadog\DatadogStatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemoryStatsRecord;
use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;
use Cosmastech\StatsDClientAdapter\Clients\Datadog\DatadogLoggingClient;
use DataDog\BatchedDogStatsd;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;

class StatsDAdapterServiceProviderTest extends AbstractTestCase
{
    private int $initialBufferSize;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->initialBufferSize = BatchedDogStatsd::$maxBufferLength;
    }

    #[\Override]
    protected function tearDown(): void
    {
        BatchedDogStatsd::$maxBufferLength = $this->initialBufferSize;
        parent::tearDown();
    }

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

    #[Test]
    public function passesDefaultTagsToAdapterManager(): void
    {
        // Given
        $defaultTags = [
            "my_first_tag" => 1,
            "my_second_tag" => 2,
            "my_third_tag" => 3.0,
        ];
        Config::set("statsd-adapter.default_tags", $defaultTags);

        // When
        $clientAdapter = $this->app->make(StatsDClientAdapter::class);

        // Then
        self::assertEqualsCanonicalizing($defaultTags, $clientAdapter->getDefaultTags());
    }

    #[Test]
    public function inMemoryStatsRecord_isSingleton(): void
    {
        // Given
        $firstRecord = $this->app->make(InMemoryStatsRecord::class);

        // When
        $secondRecord = $this->app->make(InMemoryStatsRecord::class);

        // Then
        self::assertSame($firstRecord, $secondRecord);
    }

    #[Test]
    public function datadogConfigHasBatchSize_resolve_setsBufferSize(): void
    {
        // Given
        Config::set('statsd-adapter.channels.datadog.batch_size', 2);
        Config::set('statsd-adapter.default', 'datadog');

        // When
        $clientAdapter = $this->app->make(StatsDClientAdapter::class);

        // Then
        self::assertInstanceOf(BatchedDogStatsd::class, $clientAdapter->getClient());
        self::assertEquals(2, BatchedDogStatsd::$maxBufferLength);
    }

    #[Test]
    public function batchedDogStats_terminatingApp_submitsStats(): void
    {
        // Given
        Config::set('statsd-adapter.channels.datadog.batch_size', 2);
        Config::set('statsd-adapter.default', 'datadog');

        // And
        $clientAdapter = $this->app->make(StatsDClientAdapter::class);
        $clientAdapter->increment('testing');

        // When
        $this->app->terminate();

        // Then

    }
}
