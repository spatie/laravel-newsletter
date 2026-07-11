<?php

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Spatie\Newsletter\Drivers\BeehiivDriver;
use Spatie\Newsletter\Facades\Newsletter;

it('can get the Beehiiv API', function () {
    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');

    expect(Newsletter::getApi())->toBeInstanceOf(PendingRequest::class);
});

it('can subscribe an email', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'active',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::subscribe('test@example.com');

    expect($result)->toBeArray()
        ->and($result['email'])->toBe('test@example.com')
        ->and($result['status'])->toBe('active');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.beehiiv.com/v2/publications/pub_test123/subscriptions'
            && $request->method() === 'POST'
            && $request['email'] === 'test@example.com';
    });
});

it('can subscribe an email with custom fields', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'active',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    Newsletter::subscribe('test@example.com', ['First Name' => 'John', 'Last Name' => 'Doe']);

    Http::assertSent(function ($request) {
        return $request['email'] === 'test@example.com'
            && $request['custom_fields'][0]['name'] === 'First Name'
            && $request['custom_fields'][0]['value'] === 'John'
            && $request['custom_fields'][1]['name'] === 'Last Name'
            && $request['custom_fields'][1]['value'] === 'Doe';
    });
});

it('can subscribe or update an email', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'active',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::subscribeOrUpdate('test@example.com', ['First Name' => 'John']);

    expect($result)->toBeArray()
        ->and($result['email'])->toBe('test@example.com');

    Http::assertSent(function ($request) {
        return $request['email'] === 'test@example.com'
            && $request['reactivate_existing'] === true;
    });
});

it('falls back to update when subscribe fails with 400 in subscribeOrUpdate', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions' => Http::response([
            'errors' => [['message' => 'Subscription already exists', 'code' => 'conflict']],
        ], 400),
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'active',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::subscribeOrUpdate('test@example.com', ['First Name' => 'John']);

    expect($result)->toBeArray()
        ->and($result['email'])->toBe('test@example.com');

    Http::assertSent(function ($request) {
        return $request->method() === 'PUT'
            && $request['unsubscribe'] === false;
    });
});

it('does not fall back to update when subscribe fails for a reason other than 400 in subscribeOrUpdate', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions' => Http::response([
            'errors' => [['message' => 'Unauthorized', 'code' => 'unauthorized']],
        ], 401),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::subscribeOrUpdate('test@example.com', ['First Name' => 'John']);

    expect($result)->toBeFalse()
        ->and(Newsletter::lastActionSucceeded())->toBeFalse()
        ->and(Newsletter::getLastError())->not->toBeFalse();

    Http::assertNotSent(function ($request) {
        return $request->method() === 'PUT';
    });
});

it('can unsubscribe an email', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'inactive',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::unsubscribe('test@example.com');

    expect($result)->toBeArray()
        ->and($result['status'])->toBe('inactive');

    Http::assertSent(function ($request) {
        return $request->method() === 'PUT'
            && $request['unsubscribe'] === true;
    });
});

it('does not call the API when unsubscribing a nonexistent member', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'errors' => [['message' => 'Not Found', 'code' => 'not_found']],
        ], 404),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::unsubscribe('nonexistent@example.com');

    expect($result)->toBeFalse();

    Http::assertNotSent(function ($request) {
        return $request->method() === 'PUT';
    });
});

it('can get a member', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'active',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::getMember('test@example.com');

    expect($result)->toBeArray()
        ->and($result['email'])->toBe('test@example.com');
});

it('returns false when member is not found', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'errors' => [['message' => 'Not Found', 'code' => 'not_found']],
        ], 404),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::getMember('nonexistent@example.com');

    expect($result)->toBeFalse();
});

it('can check if a member exists', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'active',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    expect(Newsletter::hasMember('test@example.com'))->toBeTrue();
});

it('can check if a member is subscribed', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'active',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    expect(Newsletter::isSubscribed('test@example.com'))->toBeTrue();
});

it('returns false for isSubscribed when member is inactive', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'inactive',
            ],
        ], 200),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    expect(Newsletter::isSubscribed('test@example.com'))->toBeFalse();
});

it('can delete a member', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'data' => [
                'id' => 'sub_00000000-0000-0000-0000-000000000000',
                'email' => 'test@example.com',
                'status' => 'active',
            ],
        ], 200),
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/sub_00000000-0000-0000-0000-000000000000' => Http::response(null, 204),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::delete('test@example.com');

    expect($result)->toBeTrue();
});

it('returns false when deleting a nonexistent member', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'errors' => [['message' => 'Not Found', 'code' => 'not_found']],
        ], 404),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::delete('nonexistent@example.com');

    expect($result)->toBeFalse();
});

it('preserves the real error when looking up the member fails during delete', function () {
    Http::fake([
        'api.beehiiv.com/v2/publications/pub_test123/subscriptions/by_email/*' => Http::response([
            'errors' => [['message' => 'Internal Server Error', 'code' => 'server_error']],
        ], 500),
    ]);

    config()->set('newsletter.driver', BeehiivDriver::class);
    config()->set('newsletter.driver_arguments.api_key', 'test-api-key');
    config()->set('newsletter.lists.subscribers.id', 'pub_test123');

    $result = Newsletter::delete('test@example.com');

    expect($result)->toBeFalse()
        ->and(Newsletter::lastActionSucceeded())->toBeFalse()
        ->and(Newsletter::getLastError())->not->toBeFalse();
});
