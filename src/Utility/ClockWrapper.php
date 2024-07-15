<?php

namespace Cosmastech\LaravelStatsDAdapter\Utility;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class ClockWrapper implements ClockInterface
{
    /**
     * @inheritDoc
     */
    public function now(): DateTimeImmutable
    {
        return now()->toDateTimeImmutable();
    }
}
