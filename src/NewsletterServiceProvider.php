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
        $this->app->bind(NewsletterListCollection::class, function () {
            return NewsletterListCollection::makeForConfig(config('laravel-newsletter'));
        });

        $this->app->singleton('laravel-newsletter-mailchimp', function () {
             return new Mailchimp(config('laravel-newsletter.mailChimp.apiKey'));
        });

        $this->app->bind(MailChimp::class, 'laravel-newsletter-mailchimp');
    }
}
