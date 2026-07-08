<?php

namespace Spatie\Newsletter\Drivers;

use Brevo\Brevo;
use Brevo\Contacts\Requests\CreateContactRequest;
use Brevo\Contacts\Requests\RemoveContactFromListRequest;
use Brevo\Contacts\Types\RemoveContactFromListRequestBodyEmails;
use Exception;
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
        $this->brevo = new Brevo(apiKey: $arguments['api_key'] ?? '');

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
            $listId = $this->lists->findByName($listName)->getId();

            $this->brevo->contacts->removeContactFromList((int) $listId, new RemoveContactFromListRequest([
                'body' => new RemoveContactFromListRequestBodyEmails(['emails' => [$email]]),
            ]));

            return $this->getMember($email, $listName);
        });
    }

    public function delete(string $email, string $listName = ''): bool
    {
        return (bool) $this->tryRequest(function () use ($email) {
            $this->brevo->contacts->deleteContact($email);

            return true;
        });
    }

    public function getMember(string $email, string $listName = ''): array|false
    {
        return $this->tryRequest(function () use ($email) {
            $contact = $this->brevo->contacts->getContactInfo($email);

            return $contact ? $contact->jsonSerialize() : false;
        });
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        $contact = $this->getMember($email, $listName);

        if (! $contact) {
            return false;
        }

        if ($listName === '') {
            return true;
        }

        $listId = $this->lists->findByName($listName)->getId();

        return in_array((int) $listId, $contact['listIds'] ?? [], true);
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        $contact = $this->getMember($email, $listName);

        if (! $contact || ($contact['emailBlacklisted'] ?? false)) {
            return false;
        }

        $listId = $this->lists->findByName($listName)->getId();

        return in_array((int) $listId, $contact['listIds'] ?? [], true);
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
            $listId = $this->lists->findByName($listName)->getId();

            $this->brevo->contacts->createContact(new CreateContactRequest(array_merge([
                'email' => $email,
                'attributes' => $properties,
                'listIds' => [(int) $listId],
                'updateEnabled' => $updateEnabled,
            ], $options)));

            return $this->getMember($email, $listName);
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
}
