<?php

use Spatie\Newsletter\Support\NewsletterList;

beforeEach(function () {
    $this->newsletterList = new NewsletterList('subscribers', [
        'id' => 'abc123',
        'marketing_permissions' => [
            'email' => 'abc123',
        ],
    ]);
});

it('it can determine the name of the list', function () {
    expect($this->newsletterList->getName())->toBe('subscribers');
});

it('it can determine the id of the list', function () {
    expect($this->newsletterList->getId())->toBe('abc123');
});
