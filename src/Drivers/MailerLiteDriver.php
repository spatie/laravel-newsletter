<?php

namespace Spatie\Newsletter\Drivers;

use MailerLite\MailerLite;
use Spatie\Newsletter\Support\Lists;

class MailerLiteDriver implements Driver
{
    protected MailerLite $mailerLite;
    protected Lists $lists;

    public static function make(array $arguments, Lists $lists): self
    {
        return new self($arguments, $lists);
    }

    public function __construct(array $arguments, Lists $lists)
    {
        $this->mailerLite = new MailerLite([
            'api_key' => $arguments['api_key'] ?? ''
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
        array $options = [],
        bool $unsubscribe = false
    ): array|false {
        $groupId = $this->lists->findByName($listName)->getId();

        try {
            $response = $this->mailerLite->subscribers->create([
                'email' => $email,
                'groups' => [$groupId],
                'fields' => $properties,
                'status' => $unsubscribe ? 'unsubscribed' : null,
            ]);

            return $response['body']['data'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): array|false {
        return $this->subscribe($email, $properties, $listName, $options);
    }

    public function unsubscribe(string $email, string $listName = ''): array|false
    {
        if ($listName) {
            $subscriber = $this->getMember($email, $listName);

            if ($subscriber) {
                $groupId = $this->lists->findByName($listName)->getId();
                $this->mailerLite->groups->unAssignSubscriber($groupId, $subscriber['id']);

                return $this->getMember($email);
            }

            return false;
        }

        return $this->subscribe($email, listName: $listName, unsubscribe: true);
    }

    public function delete(string $email, string $listName = ''): bool
    {
        $subscriber = $this->getMember($email, $listName);

        if ($subscriber) {
            try {
                $this->mailerLite->subscribers->delete($subscriber['id']);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    public function getMember(string $email, string $listName = ''): array|false
    {
        try {
            $response = $this->mailerLite->subscribers->find($email);

            $groupId = $this->lists->findByName($listName)->getId();

            return $this->matchGroup($response, $groupId);
        } catch (\Exception $e) {
            return false;
        }
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
