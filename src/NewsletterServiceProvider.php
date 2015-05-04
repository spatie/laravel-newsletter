<?php namespace Spatie\Newsletter;

use Illuminate\Support\ServiceProvider;
use Mailchimp;

class NewsletterServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
        $this->bind('Spatie\Newsletter\MailChimp\MailChimpApi',
            new Mailchimp($this->app['config']->get('newsletter.mailChimp.apiKey'))
        );

        $this->app->bind(
            'Spatie\Newsletter\Interfaces\NewsletterListInterface',
            'Spatie\Newsletter\MailChimp\NewsletterList'
        );

        $this->app->bind(
            'Spatie\Newsletter\Interfaces\NewsletterCampaignInterface',
            'Spatie\Newsletter\MailChimp\NewsletterCampaign'
        );

        $this->app->bind(
            'laravel-newsletter',
            'Spatie\Newsletter\MailChimp\Newsletter'
        );
    }
}
