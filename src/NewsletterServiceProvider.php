<?php namespace Spatie\Newsletter;

use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Spatie\Newsletter\NewsletterList',
            'Spatie\Newsletter\MailChimp\NewsletterList'
        );

        $this->app->bind(
            'Spatie\Newsletter\NewsletterCampaign',
            'Spatie\Newsletter\MailChimp\NewsletterCampaign'
        );

        $this->bind('Spatie\Newsletter\MailChimp\MailChimpApi',
            new \Mailchimp($this->app['config']->get('newsletter.mailChimp.apiKey'))
        );
    }
}
