<?php

namespace Spatie\Newsletter\Drivers;

use Spatie\Newsletter\Support\Lists;

interface Driver
{
    public static function make(array $arguments, Lists $lists);

    public function getApi();

    public function subscribe(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    );

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    );

    public function unsubscribe(string $email, string $listName = '');

    public function delete(string $email, string $listName = '');

    public function getMember(string $email, string $listName = '');

    public function hasMember(string $email, string $listName = ''): bool;

    public function isSubscribed(string $email, string $listName = ''): bool;
}
