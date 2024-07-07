<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests;

use Cosmastech\LaravelStatsDAdapter\AdapterManager;
use Cosmastech\LaravelStatsDAdapter\Stats;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use DateTimeImmutable;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;

class StatsTest extends AbstractTestCase
{
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
}
