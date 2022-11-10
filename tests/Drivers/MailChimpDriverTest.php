<?php

use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\Drivers\MailChimpDriver;
use Spatie\Newsletter\Facades\Newsletter;

it('can get the MailChimp API', function () {
    config()->set('newsletter.driver', MailChimpDriver::class);

    // this avoids MailChimp from throwing an exception
    // without an API key
    config()->set('newsletter.driver_arguments.endpoint', '');

    expect(Newsletter::getApi())->toBeInstanceOf(MailChimp::class);
});
