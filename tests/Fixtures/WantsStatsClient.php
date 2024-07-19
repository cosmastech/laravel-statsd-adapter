<?php

namespace Cosmastech\LaravelStatsDAdapter\Tests\Fixtures;

use Cosmastech\StatsDClientAdapter\Adapters\Concerns\StatsClientAwareTrait;
use Cosmastech\StatsDClientAdapter\Adapters\Contracts\StatsClientAwareInterface;
use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;

class WantsStatsClient implements StatsClientAwareInterface
{
    use StatsClientAwareTrait;

    public function getStatsClient(): StatsDClientAdapter
    {
        return $this->statsClient;
    }
}
