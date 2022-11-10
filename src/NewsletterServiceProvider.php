<?php

namespace Spatie\Newsletter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Newsletter\Support\Lists;
use Spatie\Newsletter\Drivers\Driver;

class NewsletterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-newsletter')
            ->hasConfigFile();
    }

    public function registeringPackage()
    {
        $this->app->singleton('newsletter', concrete: function () {
            /** @var class-string<Driver> $driverClass */
            $driverClass = config('newsletter.driver');

            $arguments = config('newsletter.driver_arguments');
            $lists = Lists::createFromConfig(config('newsletter'));

            return $driverClass::make($arguments, $lists);
        });
    }
}
