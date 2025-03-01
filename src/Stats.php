<?php

namespace Cosmastech\LaravelStatsDAdapter;

use Illuminate\Support\Facades\Facade;
use UnitEnum;

/**
 * @method static \Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter channel(string|null $name = null)
 * @method static void getDefaultInstance()
 * @method static void setDefaultInstance(string $name)
 * @method static array|null getInstanceConfig(string $name)
 * @method static void setDefaultTags(array $tags)
 * @method static array getDefaultTags()
 * @method static mixed instance(string|null $name = null)
 * @method static \Cosmastech\LaravelStatsDAdapter\AdapterManager forgetInstance(array|string|null $name = null)
 * @method static void purge(string|null $name = null)
 * @method static \Cosmastech\LaravelStatsDAdapter\AdapterManager extend(string $name, \Closure $callback)
 * @method static \Cosmastech\LaravelStatsDAdapter\AdapterManager setApplication(\Illuminate\Contracts\Foundation\Application $app)
 * @method static void timing(string|UnitEnum $stat, float $durationMs, float $sampleRate = 1, array $tags = [])
 * @method static mixed time(callable $closure, string|UnitEnum $stat, float $sampleRate = 1, array $tags = [])
 * @method static void gauge(string|UnitEnum $stat, float $value, float $sampleRate = 1, array $tags = [])
 * @method static void histogram(string|UnitEnum $stat, float $value, float $sampleRate = 1, array $tags = [])
 * @method static void distribution(string|UnitEnum $stat, float $value, float $sampleRate = 1, array $tags = [])
 * @method static void set(string|UnitEnum $stat, float|string $value, float $sampleRate = 1, array $tags = [])
 * @method static void increment(string|UnitEnum|array $stats, float $sampleRate = 1, array $tags = [], int $value = 1)
 * @method static void decrement(string|UnitEnum|array $stats, float $sampleRate = 1, array $tags = [], int $value = 1)
 * @method static void updateStats(string|UnitEnum|array $stats, int $delta = 1, float $sampleRate = 1, array $tags = [])
 * @method static mixed getClient()
 *
 * @see \Cosmastech\LaravelStatsDAdapter\AdapterManager
 */
class Stats extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AdapterManager::class;
    }
}
