<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests\Adapters;

use Cosmastech\LaravelStatsDAdapter\Adapters\EventDispatchingStatsRecord;
use Cosmastech\LaravelStatsDAdapter\Events\TimingRecordedEvent;
use Cosmastech\LaravelStatsDAdapter\Tests\AbstractTestCase;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\InMemoryClientAdapter;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemoryTimingRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Testing\Fakes\EventFake;
use PHPUnit\Framework\Attributes\Test;

class EventDispatchingStatsRecordTest extends AbstractTestCase
{
    private EventFake $dispatcher;
    private EventDispatchingStatsRecord $eventDispatchingStatsRecord;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = Event::fake();

        $this->eventDispatchingStatsRecord = new EventDispatchingStatsRecord($this->dispatcher);
    }

    #[Test]
    public function time_dispatchesTimingRecordedEvent(): void
    {
        // Given
        $inMemoryClient = new InMemoryClientAdapter([], $this->eventDispatchingStatsRecord);

        // And
        $closure = fn () => ["hello" => "world"];

        // When
        $output = $inMemoryClient->time($closure, "my-timing-stat", 0.92, [
            "my-tag" => "my-value",
        ]);

        // Then output should be returned from the client
        self::assertEquals(["hello" => "world"], $output);

        // And an event should be dispatched
        /** @var Collection<int, array<int, TimingRecordedEvent>> $eventsCollection */
        $eventsCollection = $this->dispatcher->dispatched(TimingRecordedEvent::class);
        self::assertCount(1, $eventsCollection);
        self::assertCount(1, $eventsCollection->first());

        /** @var TimingRecordedEvent $event */
        $event = $eventsCollection->first()[0];

        // And the record should have expected properties
        $record = $event->record;
        self::assertInstanceOf(InMemoryTimingRecord::class, $record);
        self::assertEquals("my-timing-stat", $record->stat);
        self::assertEquals(0.92, $record->sampleRate);
        self::assertEquals(["my-tag" => "my-value"], $record->tags);
    }
}
