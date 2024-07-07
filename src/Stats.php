<?php

namespace Cosmastech\LaravelStatsDAdapter;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void getDefaultInstance()
 * @method static void setDefaultInstance(string $name)
 * @method static array|null getInstanceConfig(string $name)
 * @method static mixed instance(string|null $name = null)
 * @method static \Cosmastech\LaravelStatsDAdapter\AdapterManager forgetInstance(array|string|null $name = null)
 * @method static void purge(string|null $name = null)
 * @method static \Cosmastech\LaravelStatsDAdapter\AdapterManager extend(string $name, \Closure $callback)
 * @method static void timing(string $stat, float $durationMs, float $sampleRate = 1, array $tags = [])
 * @method static void gauge(string $stat, float $value, float $sampleRate = 1, array $tags = [])
 * @method static void histogram(string $stat, float $value, float $sampleRate = 1, array $tags = [])
 * @method static void distribution(string $stat, float $value, float $sampleRate = 1, array $tags = [])
 * @method static void set(string $stat, string|float $value, float $sampleRate = 1, array $tags = [])
 * @method static void increment(array|string $stats, float $sampleRate = 1, array $tags = [], int $value = 1)
 * @method static void decrement(array|string $stats, float $sampleRate = 1, array $tags = [], int $value = 1)
 * @method static void updateStats(array|string $stats, int $delta = 1, void $sampleRate = 1, void $tags = null)
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
