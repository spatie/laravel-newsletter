<?php

namespace Spatie\Newsletter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Newsletter\Drivers\Driver;
use Spatie\Newsletter\Drivers\NullDriver;
use Spatie\Newsletter\Support\Lists;

class NewsletterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-newsletter')
            ->hasConfigFile();
    }

    public function bootingPackage()
    {
        $this->app->singleton('newsletter', function () {
            /** @var class-string<Driver> $driverClass */
            $driverClass = config('newsletter.driver');

            if (
                is_null($driverClass)
                || $driverClass === 'log'
                || $driverClass === NullDriver::class
            ) {
                return new NullDriver($driverClass === 'log');
            }

            $arguments = config('newsletter.driver_arguments');
            $lists = Lists::createFromConfig(config('newsletter'));

            return $driverClass::make($arguments, $lists);
        });
    }
}
