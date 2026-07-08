<?php

use Brevo\Brevo;
use Spatie\Newsletter\Drivers\BrevoDriver;
use Spatie\Newsletter\Facades\Newsletter;

it('can get the Brevo API', function () {
    config()->set('newsletter.driver', BrevoDriver::class);

    expect(Newsletter::getApi())->toBeInstanceOf(Brevo::class);
});
