<?php

use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\Drivers\MailChimpDriver;
use Spatie\Newsletter\Facades\Newsletter;

it('can get the MailChimp API', function () {
    config()->set('newsletter.driver', MailChimpDriver::class);

    expect(Newsletter::getApi())->toBeInstanceOf(MailChimp::class);
});
