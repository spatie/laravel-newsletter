<?php

namespace Spatie\Newsletter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MailcoachSdk\MailcoachSdkServiceProvider;
use Spatie\Newsletter\NewsletterServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            NewsletterServiceProvider::class,
        ];
    }
}
