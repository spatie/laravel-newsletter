<?php

namespace Spatie\Newsletter\Facades;

use Illuminate\Support\Facades\Facade;

class Newsletter extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'newsletter';
    }
}
