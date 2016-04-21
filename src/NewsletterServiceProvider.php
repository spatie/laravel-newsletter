<?php

namespace Spatie\Newsletter;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    protected $defer = false;


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
