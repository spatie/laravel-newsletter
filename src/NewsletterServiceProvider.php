<?php

namespace Spatie\Newsletter;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/newsletter.php', 'newsletter');

        $this->publishes([
            __DIR__.'/../config/newsletter.php' => config_path('newsletter.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(Newsletter::class, function () {
            $driver = config('newsletter.driver', 'api');
            if (is_null($driver) || $driver === 'log') {
                return new NullDriver($driver === 'log');
            }

            $mailChimp = new Mailchimp(config('newsletter.apiKey'));

            $mailChimp->verify_ssl = config('newsletter.ssl', true);

            $configuredLists = NewsletterListCollection::createFromConfig(config('newsletter'));

            return new Newsletter($mailChimp, $configuredLists);
        });

        $this->app->alias(Newsletter::class, 'newsletter');

        $this->commands([
            NewsletterPermissions::class,
        ]);
    }
}
