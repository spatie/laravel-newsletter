<?php

namespace Spatie\Newsletter;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-newsletter.php', 'laravel-newsletter');

        $this->publishes([
            __DIR__.'/../config/laravel-newsletter.php' => config_path('laravel-newsletter.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(Newsletter::class, function () {
            $mailChimp = new Mailchimp(config('laravel-newsletter.apiKey'));

            $mailChimp->verify_ssl = config('laravel-newsletter.ssl', true);

            $configuredLists = NewsletterListCollection::createFromConfig(config('laravel-newsletter'));

            return new Newsletter($mailChimp, $configuredLists);
        });

        $this->app->alias(Newsletter::class, 'laravel-newsletter');
    }
}
