<?php

namespace Cosmastech\LaravelStatsDAdapter\Adapters;

use Cosmastech\LaravelStatsDAdapter\Events\CountRecordedEvent;
use Cosmastech\LaravelStatsDAdapter\Events\DistributionRecordedEvent;
use Cosmastech\LaravelStatsDAdapter\Events\GaugeRecordedEvent;
use Cosmastech\LaravelStatsDAdapter\Events\HistogramRecordedEvent;
use Cosmastech\LaravelStatsDAdapter\Events\SetRecordedEvent;
use Cosmastech\LaravelStatsDAdapter\Events\StatRecordedEvent;
use Cosmastech\LaravelStatsDAdapter\Events\TimingRecordedEvent;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemoryCountRecord;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemoryDistributionRecord;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemoryGaugeRecord;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemoryHistogramRecord;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemorySetRecord;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemoryStatsRecord;
use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\InMemoryTimingRecord;
use Illuminate\Events\Dispatcher;

class EventDispatchingStatsRecord extends InMemoryStatsRecord
{
    protected readonly Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        parent::__construct();
    }

    public function recordTiming(InMemoryTimingRecord $inMemoryTimingRecord): void
    {
        $this->dispatch(new TimingRecordedEvent($inMemoryTimingRecord));
    }

    public function recordCount(InMemoryCountRecord $inMemoryCountRecord): void
    {
        $this->dispatch(new CountRecordedEvent($inMemoryCountRecord));
    }

    public function recordGauge(InMemoryGaugeRecord $inMemoryGaugeRecord): void
    {
        $this->dispatch(new GaugeRecordedEvent($inMemoryGaugeRecord));
    }

    public function recordSet(InMemorySetRecord $inMemorySetRecord): void
    {
        $this->dispatch(new SetRecordedEvent($inMemorySetRecord));
    }

    public function recordHistogram(InMemoryHistogramRecord $inMemoryHistogramRecord): void
    {
        $this->dispatch(new HistogramRecordedEvent($inMemoryHistogramRecord));
    }

    public function recordDistribution(InMemoryDistributionRecord $inMemoryDistributionRecord): void
    {
        $this->dispatch(new DistributionRecordedEvent($inMemoryDistributionRecord));
    }

    protected function dispatch(StatRecordedEvent $statRecordedEvent): void
    {
        $this->dispatcher->dispatch($statRecordedEvent);
    }
}
