<?php

namespace Spatie\Newsletter\Drivers;

use Exception;
use Spatie\MailcoachSdk\Mailcoach;
use Spatie\MailcoachSdk\Resources\Subscriber;
use Spatie\Newsletter\Exceptions\InvalidNewsletterList;
use Spatie\Newsletter\Support\Lists;

class MailcoachDriver implements Driver
{
    protected Mailcoach $mailcoach;

    protected Lists $lists;


    public static function make(array $arguments, Lists $lists): self
    {
        return new self($arguments, $lists);
    }

    public function __construct(array $arguments, Lists $lists)
    {
        $this->mailcoach = new Mailcoach($arguments['api_token'], $arguments['endpoint']);

        $this->lists = $lists;
    }

    public function getApi(): Mailcoach
    {
        return $this->mailcoach;
    }

    public function subscribe(
        string $email,
        array $properties,
        string $listName = '',
        array $options = []
    ): Subscriber {
        $emailListUuid = $this->lists->findByName($listName);

        $properties['email_list_uuid'] = $emailListUuid;

        return $this->mailcoach->createSubscriber($email, $properties);
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties,
        string $listName = '',
        array $options = []
    ): Subscriber{
        try {
            $subscriber = $this->subscribe($email, $properties, $listName, $options);
        } catch(Exception) {
            $subscriber = $this->getMember($email, $listName);

            foreach($properties as $name => $value)
            {
                $subscriber->$name = $value;
            }

            $subscriber->save();
        }

        return $subscriber;
    }

    public function unsubscribe(string $email, string $listName = ''): void
    {
        $this->getMember($email, $listName)?->unsubscribe();
    }

    public function delete(string $email, string $listName = '')
    {
        $this->getMember($email, $listName)?->delete();
    }

    public function getMember(string $email, string $listName = ''): ?Subscriber
    {
        $uuid = $this->lists->findByName($listName)->getId();

        $emailList = $this->mailcoach->emailList($uuid);

        if (! $emailList) {
            throw InvalidNewsletterList::noListWithId($uuid, $listName);
        }

        return $emailList->subscriber($email);
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        return $this->getMember($email, $listName) !== null;
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        $subscriber = $this->getMember($email, $listName);

        if (! $subscriber) {
            return false;
        }

        return ! empty($subscriber->subscribedAt);
    }

}
