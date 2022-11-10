<?php

namespace Spatie\Newsletter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Newsletter\Support\Lists;

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
        $this->app->singleton('newsletter', function () {
            /** @var class-string<\Spatie\Newsletter\Drivers\Driver> $driverClass */
            $driverClass = config('newsletter.driver');

            $arguments = config('newsletter.driver_arguments');
            $lists = Lists::createFromConfig(config('newsletter'));

            return $driverClass::make($arguments, $lists);
        });

        /*
        $this->app->singleton(Newsletter::class, function () {
            $driver = config('newsletter.driver', 'api');
            if (is_null($driver) || $driver === 'log') {
                return new LogDriver($driver === 'log');
            }

            $mailChimp = new Mailchimp(config('newsletter.apiKey'));

            $mailChimp->verify_ssl = config('newsletter.ssl', true);

            $configuredLists = Lists::createFromConfig(config('newsletter'));

            return new Newsletter($mailChimp, $configuredLists);
        });

        $this->app->alias(Newsletter::class, 'newsletter');
        */
    }
}
