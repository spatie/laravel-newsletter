<?php

namespace Spatie\Newsletter;

use Illuminate\Support\Facades\Facade;

class NewsletterFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'laravel-newsletter';
    }
}
