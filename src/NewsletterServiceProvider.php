<?php

namespace Spatie\Newsletter;

use Illuminate\Support\ServiceProvider;
use Mailchimp;

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
        $this->app->singleton('laravel-newsletter-mailchimp', function () {

            $apiKey = $this->app['config']->get('laravel-newsletter.mailChimp.apiKey');

            if ($apiKey) {
                return new Mailchimp($apiKey);
            }
        });

        $this->app->bind(
            'Spatie\Newsletter\Interfaces\NewsletterListInterface',
            'Spatie\Newsletter\MailChimp\NewsletterList'
        );

        $this->app->bind(
            'Spatie\Newsletter\Interfaces\NewsletterCampaignInterface',
            'Spatie\Newsletter\MailChimp\NewsletterCampaign'
        );

        $this->app->bind(
            'Spatie\Newsletter\Interfaces\NewsletterInterface',
            'Spatie\Newsletter\MailChimp\Newsletter'
        );

        $this->app->bind(
            'laravel-newsletter',
            'Spatie\Newsletter\MailChimp\Newsletter'
        );
    }
}
