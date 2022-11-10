<?php

namespace Spatie\Newsletter\Drivers;

use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\Support\Lists;

class MailChimpDriver implements Driver
{
    protected Lists $lists;

    protected MailChimp $mailChimp;

    public static function make(array $arguments, Lists $lists)
    {
        return new self($arguments, $lists);
    }

    public function __construct(array $arguments, Lists $lists)
    {
        $this->mailChimp = new MailChimp(
            $arguments['api_key'] ?? '',
            $arguments['endpoint'] ?? null,
        );

        $this->lists = $lists;
    }

    public function getApi(): MailChimp
    {
        return $this->mailChimp;
    }

    public function subscribe(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ) {
        $list = $this->lists->findByName($listName);

        $options = $this->getSubscriptionOptions($email, $properties, $options);

        $response = $this->mailChimp->post("lists/{$list->getId()}/members", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    public function subscribePending(string $email, array $properties = [], string $listName = '', array $options = [])
    {
        $options = array_merge($options, ['status' => 'pending']);

        return $this->subscribe($email, $properties, $listName, $options);
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ) {
        $list = $this->lists->findByName($listName);

        $options = $this->getSubscriptionOptions($email, $properties, $options);

        $response = $this->mailChimp->put("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    public function getMembers(string $listName = '', array $parameters = [])
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members", $parameters);
    }

    public function getMember(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}");
    }

    public function unsubscribe(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->patch("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}", [
            'status' => 'unsubscribed',
        ]);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    public function delete(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->delete("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}");

        return $response;
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        $response = $this->getMember($email, $listName);

        if (! isset($response['email_address'])) {
            return false;
        }

        if (strtolower($response['email_address']) != strtolower($email)) {
            return false;
        }

        return true;
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        $response = $this->getMember($email, $listName);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        if ($response['status'] != 'subscribed') {
            return false;
        }

        return true;
    }

    protected function getSubscriptionOptions(string $email, array $mergeFields, array $options): array
    {
        $defaultOptions = [
            'email_address' => $email,
            'status' => 'subscribed',
            'email_type' => 'html',
        ];

        if (count($mergeFields)) {
            $defaultOptions['merge_fields'] = $mergeFields;
        }

        $options = array_merge($defaultOptions, $options);

        return $options;
    }

    public function getLastError(): string|false
    {
        return $this->mailChimp->getLastError();
    }

    public function lastActionSucceeded(): bool
    {
        return $this->mailChimp->success();
    }

    protected function getSubscriberHash(string $email): string
    {
        return $this->mailChimp->subscriberHash($email);
    }
}
