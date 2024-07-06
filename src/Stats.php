<?php

namespace Cosmastech\LaravelStatsDAdapter;

use Illuminate\Support\Facades\Facade;

class Stats extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AdapterManager::class;
    }
}
