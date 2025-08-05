<?php

use MailerLite\MailerLite;
use Spatie\Newsletter\Drivers\MailerLiteDriver;
use Spatie\Newsletter\Facades\Newsletter;

it('can get the MailerLite API', function () {
    config()->set('newsletter.driver', MailerLiteDriver::class);

    expect(Newsletter::getApi())->toBeInstanceOf(MailerLite::class);
});
