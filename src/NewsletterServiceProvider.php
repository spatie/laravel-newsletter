<?php

namespace Spatie\Newsletter;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-newsletter.php' => config_path('laravel-newsletter.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton('laravel-newsletter', function () {

            $mailChimp = new Mailchimp(config('laravel-newsletter.apiKey'));

            $configuredLists = NewsletterListCollection::makeForConfig(config('laravel-newsletter'));

            return new Newsletter($mailChimp, $configuredLists);
        });
    }
}
