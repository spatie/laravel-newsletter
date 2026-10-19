<?php

namespace Spatie\Newsletter\Drivers;

use Brevo\Brevo;
use Brevo\Contacts\Requests\CreateContactRequest;
use Brevo\Contacts\Requests\RemoveContactFromListRequest;
use Brevo\Contacts\Types\RemoveContactFromListRequestBodyEmails;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Spatie\Newsletter\Support\Lists;

class BrevoDriver implements Driver
{
    protected Brevo $brevo;

    protected Lists $lists;

    protected string|false $lastError = false;

    public static function make(array $arguments, Lists $lists): self
    {
        return new self($arguments, $lists);
    }

    public function __construct(array $arguments, Lists $lists)
    {
        $this->brevo = new Brevo(
            apiKey: $arguments['api_key'] ?? '',
            options: $arguments['options'] ?? [],
        );

        $this->lists = $lists;
    }

    public function getApi(): Brevo
    {
        return $this->brevo;
    }

    public function subscribe(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): array|false {
        return $this->createContact($email, $properties, $listName, $options, false);
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): array|false {
        return $this->createContact($email, $properties, $listName, $options, true);
    }

    public function unsubscribe(string $email, string $listName = ''): array|false
    {
        return $this->tryRequest(function () use ($email, $listName) {
            $listId = $this->getListId($listName);

            $response = $this->brevo->contacts->removeContactFromList($listId, new RemoveContactFromListRequest([
                'body' => new RemoveContactFromListRequestBodyEmails(['emails' => [$email]]),
            ]));

            if ($response?->contacts->failure) {
                throw new RuntimeException("Could not remove contact from Brevo list {$listId}.");
            }

            return $this->getContact($email);
        });
    }

    public function delete(string $email, string $listName = ''): bool
    {
        return (bool) $this->tryRequest(function () use ($email) {
            $this->brevo->contacts->deleteContact(rawurlencode($email));

            return true;
        });
    }

    public function getMember(string $email, string $listName = ''): array|false
    {
        return $this->tryRequest(function () use ($email, $listName) {
            $listId = $this->getListId($listName);
            $contact = $this->getContact($email);

            if (! $contact) {
                return false;
            }

            return in_array($listId, $contact['listIds'], true) ? $contact : false;
        });
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        return $this->getMember($email, $listName) !== false;
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        $contact = $this->getMember($email, $listName);

        if (! $contact || ($contact['emailBlacklisted'] ?? false)) {
            return false;
        }

        $listId = $this->getListId($listName);

        return ! in_array($listId, $contact['listUnsubscribed'] ?? [], true);
    }

    public function getLastError(): string|false
    {
        return $this->lastError;
    }

    public function lastActionSucceeded(): bool
    {
        return $this->lastError === false;
    }

    protected function createContact(
        string $email,
        array $properties,
        string $listName,
        array $options,
        bool $updateEnabled
    ): array|false {
        return $this->tryRequest(function () use ($email, $properties, $listName, $options, $updateEnabled) {
            $listId = $this->getListId($listName);

            $this->brevo->contacts->createContact(new CreateContactRequest(array_merge($options, [
                'email' => $email,
                'attributes' => $properties,
                'listIds' => [$listId],
                'updateEnabled' => $updateEnabled,
            ])));

            return $this->getContact($email);
        });
    }

    protected function getContact(string $email): array|false
    {
        $contact = $this->brevo->contacts->getContactInfo(rawurlencode($email));

        if (! $contact) {
            return false;
        }

        return array_merge($contact->jsonSerialize(), [
            'attributes' => $contact->attributes->getAdditionalProperties(),
        ]);
    }

    protected function getListId(string $listName): int
    {
        $list = $this->lists->findByName($listName);
        $listId = filter_var($list->properties['id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($listId === false) {
            throw new InvalidArgumentException("The Brevo list id for `{$list->getName()}` must be a positive integer.");
        }

        return $listId;
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
