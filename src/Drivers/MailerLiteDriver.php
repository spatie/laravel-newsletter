<?php

namespace Spatie\Newsletter\Drivers;

use Exception;
use MailerLite\MailerLite;
use Spatie\Newsletter\Support\Lists;

class MailerLiteDriver implements Driver
{
    protected MailerLite $mailerLite;

    protected Lists $lists;

    protected string|false $lastError = false;

    public static function make(array $arguments, Lists $lists): self
    {
        return new self($arguments, $lists);
    }

    public function __construct(array $arguments, Lists $lists)
    {
        $this->mailerLite = new MailerLite([
            'api_key' => $arguments['api_key'] ?? '',
        ]);

        $this->lists = $lists;
    }

    public function getApi(): MailerLite
    {
        return $this->mailerLite;
    }

    public function subscribe(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): array|false {
        return $this->createSubscriber($email, $properties, $listName, $options);
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): array|false {
        return $this->createSubscriber($email, $properties, $listName, $options);
    }

    public function unsubscribe(string $email, string $listName = ''): array|false
    {
        $subscriber = $this->getMember($email, $listName);

        if (! $subscriber) {
            return false;
        }

        if ($listName !== '') {
            return $this->tryRequest(function () use ($listName, $subscriber, $email) {
                $groupId = $this->lists->findByName($listName)->getId();

                $this->mailerLite->groups->unAssignSubscriber($groupId, $subscriber['id']);

                return $this->getMember($email);
            });
        }

        return $this->createSubscriber($email, [], $listName, ['status' => 'unsubscribed']);
    }

    public function delete(string $email, string $listName = ''): bool
    {
        $subscriber = $this->getMember($email, $listName);

        if (! $subscriber) {
            return false;
        }

        return (bool) $this->tryRequest(function () use ($subscriber) {
            $this->mailerLite->subscribers->delete($subscriber['id']);

            return true;
        });
    }

    public function getMember(string $email, string $listName = ''): array|false
    {
        return $this->tryRequest(function () use ($email, $listName) {
            $response = $this->mailerLite->subscribers->find($email);

            $groupId = $this->lists->findByName($listName)->getId();

            return $this->matchGroup($response, $groupId);
        });
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        return $this->getMember($email, $listName) !== false;
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        $subscriber = $this->getMember($email, $listName);

        return $subscriber && ($subscriber['status'] ?? null) === 'active';
    }

    public function getLastError(): string|false
    {
        return $this->lastError;
    }

    public function lastActionSucceeded(): bool
    {
        return $this->lastError === false;
    }

    protected function createSubscriber(
        string $email,
        array $properties,
        string $listName,
        array $options
    ): array|false {
        return $this->tryRequest(function () use ($email, $properties, $listName, $options) {
            $groupId = $this->lists->findByName($listName)->getId();

            $payload = array_merge([
                'email' => $email,
                'groups' => [$groupId],
                'fields' => $properties,
            ], $options);

            $response = $this->mailerLite->subscribers->create($payload);

            return $response['body']['data'] ?? false;
        });
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

    protected function matchGroup(array $subscriber, string|int $groupId): array|false
    {
        $groups = $subscriber['body']['data']['groups'] ?? [];

        $groupIds = array_column($groups, 'id');

        if (in_array($groupId, $groupIds, true)) {
            return $subscriber['body']['data'];
        }

        return false;
    }
}
