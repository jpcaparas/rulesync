<?php

namespace JPCaparas\Rulesync\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JPCaparas\Rulesync\Rulesync
 */
class Rulesync extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JPCaparas\Rulesync\Rulesync::class;
    }
}
