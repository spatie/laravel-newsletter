<?php

use Spatie\MailcoachSdk\Mailcoach;
use Spatie\Newsletter\Facades\Newsletter;

it('can get the mailcoach API', function () {
    expect(Newsletter::getApi())->toBeInstanceOf(Mailcoach::class);
});
