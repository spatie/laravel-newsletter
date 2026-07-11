<?php

use Spatie\Newsletter\Drivers\BeehiivDriver;
use Spatie\Newsletter\Facades\Newsletter;

beforeEach(function () {
    if (! env('BEEHIIV_API_KEY') || ! env('BEEHIIV_PUBLICATION_ID')) {
        $this->markTestSkipped('Beehiiv credentials not set');
    }

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', env('BEEHIIV_API_KEY'));
    config()->set('newsletter.lists.subscribers.id', env('BEEHIIV_PUBLICATION_ID'));
});

it('can subscribe a real email', function () {
    $email = 'integration-test-' . time() . '@example.com';

    $result = Newsletter::subscribe($email, ['First Name' => 'Test', 'Last Name' => 'User']);

    expect($result)->toBeArray()
        ->and($result['email'])->toBe($email);
});

it('can get a member after subscribing', function () {
    $email = 'integration-get-' . time() . '@example.com';

    Newsletter::subscribe($email);
    sleep(2);

    $member = Newsletter::getMember($email);

    expect($member)->toBeArray()
        ->and($member['email'])->toBe($email);
});

it('can check hasMember', function () {
    $email = 'integration-has-' . time() . '@example.com';

    Newsletter::subscribe($email);
    sleep(2);

    expect(Newsletter::hasMember($email))->toBeTrue();
    expect(Newsletter::hasMember('nonexistent-' . time() . '@example.com'))->toBeFalse();
});

it('can subscribeOrUpdate an existing member', function () {
    $email = 'integration-update-' . time() . '@example.com';

    Newsletter::subscribe($email, ['First Name' => 'Original']);
    sleep(2);

    $result = Newsletter::subscribeOrUpdate($email, ['First Name' => 'Updated']);

    expect($result)->toBeArray()
        ->and($result['email'])->toBe($email);
});

it('can unsubscribe a member', function () {
    $email = 'integration-unsub-' . time() . '@example.com';

    Newsletter::subscribe($email);
    sleep(2);

    $result = Newsletter::unsubscribe($email);

    expect($result)->toBeArray();

    sleep(1);

    expect(Newsletter::isSubscribed($email))->toBeFalse();
});

it('can delete a member', function () {
    $email = 'integration-delete-' . time() . '@example.com';

    Newsletter::subscribe($email);
    sleep(2);

    $deleted = Newsletter::delete($email);

    expect($deleted)->toBeTrue();

    sleep(1);

    expect(Newsletter::hasMember($email))->toBeFalse();
});
