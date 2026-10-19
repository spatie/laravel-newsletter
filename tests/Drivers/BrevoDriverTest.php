<?php

use Brevo\Brevo;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Spatie\Newsletter\Drivers\BrevoDriver;
use Spatie\Newsletter\Facades\Newsletter;

beforeEach(function () {
    $this->responses = new MockHandler;
    $this->requests = [];
    $handler = HandlerStack::create($this->responses);
    $handler->push(Middleware::history($this->requests));

    config()->set('newsletter', [
        'driver' => BrevoDriver::class,
        'driver_arguments' => [
            'api_key' => 'test-brevo-api-key',
            'endpoint' => null,
            'options' => [
                'client' => new Client(['handler' => $handler]),
                'maxRetries' => 0,
            ],
        ],
        'default_list_name' => 'subscribers',
        'lists' => [
            'subscribers' => ['id' => '42'],
            'customers' => ['id' => '73'],
        ],
    ]);

    $this->email = 'user+tag/#?%@example.com';
    $this->contact = [
        'id' => 123,
        'email' => $this->email,
        'attributes' => ['FIRSTNAME' => 'Jane', 'AGE' => 37, 'VIP' => false],
        'emailBlacklisted' => false,
        'smsBlacklisted' => false,
        'whatsappBlacklisted' => false,
        'listIds' => [42, 73],
        'createdAt' => '2026-07-01T10:00:00Z',
        'modifiedAt' => '2026-07-02T11:00:00Z',
        'statistics' => (object) [],
    ];
});

it('resolves the Brevo API through the Laravel service provider', function () {
    expect(Newsletter::getApi())->toBeInstanceOf(Brevo::class);
});

it('maps subscriptions to the authenticated Brevo API', function (string $method, int $status, string $listName, int $listId) {
    $this->responses->append(
        new Response($status, [], $status === 201 ? '{"id":123}' : ''),
        new Response(200, [], json_encode($this->contact)),
    );

    $member = Newsletter::$method($this->email, ['FIRSTNAME' => 'Jane'], $listName, ['smsBlacklisted' => true]);

    expect($member['id'])->toBe(123)
        ->and($member['attributes'])->toBe($this->contact['attributes'])
        ->and(Newsletter::lastActionSucceeded())->toBeTrue()
        ->and(Newsletter::getLastError())->toBeFalse();

    $request = $this->requests[0]['request'];
    expect($request->getMethod())->toBe('POST')
        ->and((string) $request->getUri())->toBe('https://api.brevo.com/v3/contacts')
        ->and($request->getHeaderLine('api-key'))->toBe('test-brevo-api-key')
        ->and(json_decode((string) $request->getBody(), true))->toEqual([
            'email' => $this->email,
            'attributes' => ['FIRSTNAME' => 'Jane'],
            'listIds' => [$listId],
            'updateEnabled' => $method === 'subscribeOrUpdate',
            'smsBlacklisted' => true,
        ]);

    expect(json_decode((string) $request->getBody(), true)['listIds'])->toBe([$listId]);
})->with([
    'create on default list' => ['subscribe', 201, '', 42],
    'upsert on named list' => ['subscribeOrUpdate', 204, 'customers', 73],
]);

it('serializes empty attributes as a JSON object', function () {
    $this->responses->append(new Response(201, [], '{"id":123}'), new Response(200, [], json_encode($this->contact)));

    expect(Newsletter::subscribe($this->email))->toBeArray();

    $payload = json_decode((string) $this->requests[0]['request']->getBody());
    expect($payload->attributes)->toBeInstanceOf(stdClass::class)
        ->and($payload->updateEnabled)->toBeFalse();
});

it('keeps explicit subscription arguments authoritative over extra options', function () {
    $this->responses->append(new Response(201, [], '{"id":123}'), new Response(200, [], json_encode($this->contact)));

    Newsletter::subscribe($this->email, ['FIRSTNAME' => 'Jane'], 'customers', [
        'email' => 'someone-else@example.com',
        'attributes' => ['FIRSTNAME' => 'Other'],
        'listIds' => [99],
        'updateEnabled' => true,
    ]);

    $payload = json_decode((string) $this->requests[0]['request']->getBody(), true);
    expect($payload['email'])->toBe($this->email)
        ->and($payload['attributes'])->toBe(['FIRSTNAME' => 'Jane'])
        ->and($payload['listIds'])->toBe([73])
        ->and($payload['updateEnabled'])->toBeFalse();
});

it('returns contact attributes and URL encodes email identifiers', function () {
    $this->responses->append(new Response(200, [], json_encode($this->contact)));

    $member = Newsletter::getMember($this->email);

    expect($member['attributes'])->toBe($this->contact['attributes'])
        ->and($member['email'])->toBe($this->email)
        ->and($member['listIds'])->toBe([42, 73])
        ->and($this->requests[0]['request']->getMethod())->toBe('GET')
        ->and($this->requests[0]['request']->getUri()->getPath())->toBe('/v3/contacts/user%2Btag%2F%23%3F%25%40example.com');
});

it('scopes member lookups to the default or named list', function (string $method, string $listName, array $listIds, bool $found) {
    $this->contact['listIds'] = $listIds;
    $this->responses->append(new Response(200, [], json_encode($this->contact)));

    expect((bool) Newsletter::$method($this->email, $listName))->toBe($found)
        ->and(Newsletter::lastActionSucceeded())->toBeTrue();
})->with(['getMember', 'hasMember', 'isSubscribed'])->with([
    'default present' => ['', [42], true],
    'default absent' => ['', [73], false],
    'named present' => ['customers', [73], true],
    'named absent' => ['customers', [42], false],
]);

it('distinguishes list membership from permission to send email', function (bool $blacklisted, array $unsubscribed, bool $subscribed) {
    $this->contact['emailBlacklisted'] = $blacklisted;
    $this->contact['listUnsubscribed'] = $unsubscribed;
    $this->responses->append(
        new Response(200, [], json_encode($this->contact)),
        new Response(200, [], json_encode($this->contact)),
    );

    expect(Newsletter::hasMember($this->email))->toBeTrue()
        ->and(Newsletter::isSubscribed($this->email))->toBe($subscribed);
})->with([
    'globally blocklisted' => [true, [], false],
    'unsubscribed from default list' => [false, [42], false],
    'unsubscribed from another list' => [false, [73], true],
]);

it('removes only the selected list without blocklisting or deleting the contact', function (string $listName, int $removedId, int $remainingId) {
    $this->contact['listIds'] = [$remainingId];
    $this->responses->append(
        new Response(201, [], json_encode(['contacts' => ['success' => [$this->email], 'failure' => []]])),
        new Response(200, [], json_encode($this->contact)),
    );

    $member = Newsletter::unsubscribe($this->email, $listName);

    expect($member['listIds'])->toBe([$remainingId])
        ->and($member['emailBlacklisted'])->toBeFalse()
        ->and(Newsletter::lastActionSucceeded())->toBeTrue();

    $request = $this->requests[0]['request'];
    expect($request->getMethod())->toBe('POST')
        ->and($request->getUri()->getPath())->toBe("/v3/contacts/lists/{$removedId}/contacts/remove")
        ->and(json_decode((string) $request->getBody(), true))->toBe(['emails' => [$this->email]]);
})->with([
    'default' => ['', 42, 73],
    'named' => ['customers', 73, 42],
]);

it('reports a per-contact removal failure even when the HTTP request succeeds', function () {
    $this->responses->append(
        new Response(201, [], json_encode(['contacts' => ['success' => [], 'failure' => [$this->email]]])),
        new Response(200, [], json_encode($this->contact)),
    );

    expect(Newsletter::unsubscribe($this->email))->toBeFalse()
        ->and(Newsletter::lastActionSucceeded())->toBeFalse()
        ->and(Newsletter::getLastError())->toContain('remove');
});

it('preserves follow-up lookup failures after a successful mutation', function (string $method) {
    $body = $method === 'unsubscribe'
        ? json_encode(['contacts' => ['success' => [$this->email]]])
        : '{"id":123}';
    $this->responses->append(
        new Response(201, [], $body),
        new Response(404, [], '{"code":"document_not_found","message":"Contact not found"}'),
    );

    expect(Newsletter::$method($this->email))->toBeFalse()
        ->and(Newsletter::lastActionSucceeded())->toBeFalse()
        ->and(Newsletter::getLastError())->toBeString()->not->toBeEmpty();
})->with(['subscribe', 'subscribeOrUpdate', 'unsubscribe']);

it('deletes the account-wide contact using its encoded email', function () {
    $this->responses->append(new Response(204));

    expect(Newsletter::delete($this->email))->toBeTrue()
        ->and(Newsletter::lastActionSucceeded())->toBeTrue()
        ->and($this->requests[0]['request']->getMethod())->toBe('DELETE')
        ->and($this->requests[0]['request']->getUri()->getPath())->toBe('/v3/contacts/user%2Btag%2F%23%3F%25%40example.com');
});

it('reports API failures and clears the error after a successful action', function (string $method, int $status) {
    $this->responses->append(
        new Response($status, [], '{"code":"invalid_parameter","message":"Request failed"}'),
        new Response(200, [], json_encode($this->contact)),
    );

    expect(Newsletter::$method($this->email))->toBeFalse()
        ->and(Newsletter::lastActionSucceeded())->toBeFalse()
        ->and(Newsletter::getLastError())->toBeString()->not->toBeEmpty();

    expect(Newsletter::getMember($this->email))->toBeArray()
        ->and(Newsletter::lastActionSucceeded())->toBeTrue()
        ->and(Newsletter::getLastError())->toBeFalse();
})->with(['subscribe', 'subscribeOrUpdate', 'unsubscribe', 'getMember', 'delete'])
    ->with([400, 401, 404, 429, 500]);

it('reports transport failures', function () {
    $this->responses->append(new ConnectException('Connection failed', new Request('GET', 'https://api.brevo.com/v3/contacts')));

    expect(Newsletter::getMember($this->email))->toBeFalse()
        ->and(Newsletter::lastActionSucceeded())->toBeFalse()
        ->and(Newsletter::getLastError())->toBeString()->not->toBeEmpty();
});

it('rejects invalid list configuration before sending a request', function (string $listName, mixed $listId) {
    config()->set('newsletter.lists.subscribers.id', $listId);
    $this->responses->append(new Response(201, [], '{"id":123}'), new Response(200, [], json_encode($this->contact)));

    expect(Newsletter::subscribe($this->email, listName: $listName))->toBeFalse()
        ->and(Newsletter::lastActionSucceeded())->toBeFalse()
        ->and(Newsletter::getLastError())->toBeString()->not->toBeEmpty()
        ->and($this->requests)->toBeEmpty();
})->with([
    'unknown list' => ['missing', '42'],
    'missing id' => ['', null],
    'zero id' => ['', '0'],
    'negative id' => ['', '-3'],
    'non-integer id' => ['', '42abc'],
]);
