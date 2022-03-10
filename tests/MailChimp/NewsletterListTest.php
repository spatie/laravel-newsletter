<?php

namespace Spatie\Newsletter\Test;

use Spatie\Newsletter\NewsletterList;

beforeEach(function () {
    $this->newsletterList = new NewsletterList('subscriber', [
        'id' => 'abc123',
        'marketing_permissions' => [
            'email' => 'abc123',
        ],
    ]);
});


it('it can determine the name of the list', function () {
    $this->assertSame('subscriber', $this->newsletterList->getName());
});


it('it can determine the id of the list', function () {
    $this->assertSame('abc123', $this->newsletterList->getId());
});


it('it can get a marketing permission of the list', function () {
    $this->assertSame('abc123', $this->newsletterList->getMarketingPermission('email'));
});
