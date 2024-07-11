<?php

namespace Cosmastech\LaravelStatsDAdapter\Events;

use Cosmastech\StatsDClientAdapter\Adapters\InMemory\Models\Contracts\RecordInterface;
use Illuminate\Foundation\Events\Dispatchable;

abstract class StatRecordedEvent
{
    use Dispatchable;

    public function __construct(public readonly RecordInterface $record)
    {
    }
}
