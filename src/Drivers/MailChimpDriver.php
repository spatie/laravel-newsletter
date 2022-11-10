<?php

namespace Spatie\Newsletter\Drivers;

use Spatie\Newsletter\Support\Lists;

class MailChimpDriver implements Driver
{
    protected Lists $lists;

    public static function make(array $arguments, Lists $lists)
    {
        return new self($arguments, $lists);
    }

    public function __construct(array $arguments, Lists $lists)
    {
        $this->lists = $lists;
    }

    public function getApi()
    {
        // TODO: Implement getApi() method.
    }

    public function subscribe(
        string $email,
        array $properties,
        string $listName = '',
        array $options = []
    ) {
        // TODO: Implement subscribe() method.
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties,
        string $listName = '',
        array $options = []
    ) {
        // TODO: Implement subscribeOrUpdate() method.
    }

    public function unsubscribe(string $email, string $listName = '')
    {
        // TODO: Implement unsubscribe() method.
    }

    public function delete(string $email, string $listName = '')
    {
        // TODO: Implement delete() method.
    }

    public function getMember(string $email, string $listName = '')
    {
        // TODO: Implement getMember() method.
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        // TODO: Implement hasMember() method.
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        // TODO: Implement isSubscribed() method.
    }
}
