<?php

use Spatie\Newsletter\NewsletterList;

beforeEach(function () {
    $this->newsletterList = new NewsletterList('subscriber', [
        'id' => 'abc123',
        'marketing_permissions' => [
            'email' => 'abc123',
        ],
    ]);
});

it('can determine the name of the list')
    ->expect(fn () => $this->newsletterList->getName())
    ->toBe('subscriber');

it('can determine the id of the list')
    ->expect(fn () => $this->newsletterList->getId())
    ->toBe('abc123');

it('can get a marketing permission of the list')
    ->expect(fn () => $this->newsletterList->getMarketingPermission('email'))
    ->toBe('abc123');
