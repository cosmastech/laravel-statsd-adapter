<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests;

use Cosmastech\LaravelStatsDAdapter\Tests\Fixtures\WantsStatsClient;
use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;

class StatsClientAwareServiceProviderTest extends AbstractTestCase
{
    #[Test]
    public function appResolvesStatsClientAwareInterfaceClass_classHasStatsClientSet(): void
    {
        // Given
        Config::set("statsd-adapter.default", "memory");

        // When
        $wantsStatsClient = $this->app->make(WantsStatsClient::class);

        // Then
        self::assertSame(
            $this->app->make(StatsDClientAdapter::class),
            $wantsStatsClient->getStatsClient()
        );
    }
}
