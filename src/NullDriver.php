<?php

namespace Spatie\Newsletter;

use Illuminate\Support\Facades\Log;

class NullDriver implements Newsletter
{
    /**
     * @var bool
     */
    private $logCalls;

    public function __construct(bool $logCalls = false)
    {
        $this->logCalls = $logCalls;
    }

    public function subscribe(string $email, array $mergeFields = [], string $listName = '', array $options = [])
    {
        $this->log('subscribe', func_get_args());
    }

    public function subscribePending(
        string $email,
        array $mergeFields = [],
        string $listName = '',
        array $options = []
    ) {
        $this->log('subscribePending', func_get_args());
    }

    public function subscribeOrUpdate(
        string $email,
        array $mergeFields = [],
        string $listName = '',
        array $options = []
    ) {
        $this->log('subscribeOrUpdate', func_get_args());
    }

    public function getMembers(string $listName = '', array $parameters = [])
    {
        $this->log('getMembers', func_get_args());
    }

    public function getMember(string $email, string $listName = '')
    {
        $this->log('getMember', func_get_args());
    }

    public function getMemberActivity(string $email, string $listName = '')
    {
        $this->log('getMemberActivity', func_get_args());
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        $this->log('isSubscribed', func_get_args());

        return true;
    }

    public function unsubscribe(string $email, string $listName = '')
    {
        $this->log('unsubscribe', func_get_args());
    }

    public function updateEmailAddress(string $currentEmailAddress, string $newEmailAddress, string $listName = '')
    {
        $this->log('updateEmailAddress', func_get_args());
    }

    public function delete(string $email, string $listName = '')
    {
        $this->log('delete', func_get_args());
    }

    public function getTags(string $email, string $listName = '')
    {
        $this->log('getTags', func_get_args());
    }

    public function addTags(array $tags, string $email, string $listName = '')
    {
        $this->log('addTags', func_get_args());
    }

    public function removeTags(array $tags, string $email, string $listName = '')
    {
        $this->log('removeTags', func_get_args());
    }

    public function createCampaign(
        string $fromName,
        string $replyTo,
        string $subject,
        string $html = '',
        string $listName = '',
        array $options = [],
        array $contentOptions = []
    ) {
        $this->log('createCampaign', func_get_args());
    }

    public function updateContent(string $campaignId, string $html, array $options = [])
    {
        $this->log('updateContent', func_get_args());
    }

    public function getLastError()
    {
        $this->log('getLastError', func_get_args());
    }

    public function lastActionSucceeded(): bool
    {
        $this->log('lastActionSucceeded', func_get_args());
    }

    public function deletePermanently(string $email, string $listName = '')
    {
        $this->log('deletePermanently', func_get_args());
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        $this->log('hasMember', func_get_args());

        return true;
    }

    private function log($name, $arguments)
    {
        if ($this->logCalls) {
            Log::debug('Called Spatie Newsletter facade method: '.$name.' with:', $arguments);
        }
    }
}
