<?php

use Spatie\Newsletter\Drivers\MailChimpDriver;
use Spatie\Newsletter\Facades\Newsletter;

it('cannot subscribe a user already subscribed', function () {
    config()->set('newsletter.driver', MailChimpDriver::class);

    config()->set('newsletter.driver_arguments.endpoint', '');

    $email = 'demo@laravel.com';

    Newsletter::shouldReceive('isSubscribed')
        ->with($email)
        ->andReturn(true);

    if (Newsletter::isSubscribed($email)) {
        throw new Exception("User is already subscribed");
    }
})->throws(Exception::class, "User is already subscribed");

