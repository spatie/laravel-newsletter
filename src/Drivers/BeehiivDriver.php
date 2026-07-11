<?php

namespace Spatie\Newsletter\Drivers;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Spatie\Newsletter\Support\Lists;

class BeehiivDriver implements Driver
{
    protected PendingRequest $http;

    protected Lists $lists;

    protected string|false $lastError = false;

    public static function make(array $arguments, Lists $lists): self
    {
        return new self($arguments, $lists);
    }

    public function __construct(array $arguments, Lists $lists)
    {
        $this->http = Http::baseUrl('https://api.beehiiv.com/v2')
            ->withToken($arguments['api_key'] ?? '')
            ->acceptJson()
            ->asJson();

        $this->lists = $lists;
    }

    public function getApi(): PendingRequest
    {
        return $this->http;
    }

    public function subscribe(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): array|false {
        return $this->tryRequest(function () use ($email, $properties, $listName, $options) {
            $publicationId = $this->lists->findByName($listName)->getId();

            $payload = array_merge([
                'email' => $email,
            ], $options);

            if (! empty($properties)) {
                $payload['custom_fields'] = $this->formatCustomFields($properties);
            }

            $response = $this->http->post(
                "/publications/{$publicationId}/subscriptions",
                $payload
            );

            $response->throw();

            return $response->json('data');
        });
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): array|false {
        return $this->tryRequest(function () use ($email, $properties, $listName, $options) {
            $publicationId = $this->lists->findByName($listName)->getId();

            $payload = array_merge([
                'email' => $email,
                'reactivate_existing' => true,
            ], $options);

            if (! empty($properties)) {
                $payload['custom_fields'] = $this->formatCustomFields($properties);
            }

            $response = $this->http->post(
                "/publications/{$publicationId}/subscriptions",
                $payload
            );

            if ($response->successful()) {
                return $response->json('data');
            }

            // Beehiiv has no distinct status code for "subscriber already exists",
            // so 400 is the only signal we can use to fall back to an update;
            // anything else (401, 404, 429, 500, ...) is a genuine failure.
            if ($response->status() !== 400) {
                $response->throw();
            }

            $updatePayload = [
                'unsubscribe' => false,
            ];

            if (! empty($properties)) {
                $updatePayload['custom_fields'] = $this->formatCustomFields($properties);
            }

            $updatePayload = array_merge($updatePayload, $options);

            $encodedEmail = urlencode($email);

            $updateResponse = $this->http->put(
                "/publications/{$publicationId}/subscriptions/by_email/{$encodedEmail}",
                $updatePayload
            );

            $updateResponse->throw();

            return $updateResponse->json('data');
        });
    }

    public function unsubscribe(string $email, string $listName = ''): array|false
    {
        $member = $this->getMember($email, $listName);

        if (! $member) {
            return false;
        }

        return $this->tryRequest(function () use ($email, $listName) {
            $publicationId = $this->lists->findByName($listName)->getId();
            $encodedEmail = urlencode($email);

            $response = $this->http->put(
                "/publications/{$publicationId}/subscriptions/by_email/{$encodedEmail}",
                ['unsubscribe' => true]
            );

            $response->throw();

            return $response->json('data');
        });
    }

    public function delete(string $email, string $listName = ''): bool
    {
        $member = $this->getMember($email, $listName);

        if (! $member) {
            return false;
        }

        return (bool) $this->tryRequest(function () use ($member, $listName) {
            $publicationId = $this->lists->findByName($listName)->getId();
            $subscriptionId = $member['id'];

            $response = $this->http->delete(
                "/publications/{$publicationId}/subscriptions/{$subscriptionId}"
            );

            $response->throw();

            return true;
        });
    }

    public function getMember(string $email, string $listName = ''): array|false
    {
        return $this->tryRequest(function () use ($email, $listName) {
            $publicationId = $this->lists->findByName($listName)->getId();
            $encodedEmail = urlencode($email);

            $response = $this->http->get(
                "/publications/{$publicationId}/subscriptions/by_email/{$encodedEmail}"
            );

            if ($response->status() === 404) {
                return false;
            }

            $response->throw();

            return $response->json('data');
        });
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        return $this->getMember($email, $listName) !== false;
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        $member = $this->getMember($email, $listName);

        if (! $member) {
            return false;
        }

        return ($member['status'] ?? '') === 'active';
    }

    public function getLastError(): string|false
    {
        return $this->lastError;
    }

    public function lastActionSucceeded(): bool
    {
        return $this->lastError === false;
    }

    protected function formatCustomFields(array $properties): array
    {
        $customFields = [];

        foreach ($properties as $name => $value) {
            $customFields[] = [
                'name' => $name,
                'value' => $value,
            ];
        }

        return $customFields;
    }

    protected function tryRequest(callable $callback): mixed
    {
        try {
            $result = $callback();

            $this->lastError = false;

            return $result;
        } catch (Exception $exception) {
            $this->lastError = $exception->getMessage();

            return false;
        }
    }
}
