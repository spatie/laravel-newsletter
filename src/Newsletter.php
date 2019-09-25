<?php

namespace Spatie\Newsletter;

use DrewM\MailChimp\MailChimp;
interface Newsletter
{
    public function subscribe(string $email, array $mergeFields = [], string $listName = '', array $options = []);

    public function subscribePending(
        string $email,
        array $mergeFields = [],
        string $listName = '',
        array $options = []
    );

    public function subscribeOrUpdate(
        string $email,
        array $mergeFields = [],
        string $listName = '',
        array $options = []
    );

    public function getMembers(string $listName = '', array $parameters = []);

    public function getMember(string $email, string $listName = '');

    public function getMemberActivity(string $email, string $listName = '');

    public function hasMember(string $email, string $listName = ''): bool;

    public function isSubscribed(string $email, string $listName = ''): bool;

    public function unsubscribe(string $email, string $listName = '');

    public function updateEmailAddress(string $currentEmailAddress, string $newEmailAddress, string $listName = '');

    public function delete(string $email, string $listName = '');

    public function deletePermanently(string $email, string $listName = '');

    public function getTags(string $email, string $listName = '');

    public function addTags(array $tags, string $email, string $listName = '');

    public function removeTags(array $tags, string $email, string $listName = '');

    public function createCampaign(
        string $fromName,
        string $replyTo,
        string $subject,
        string $html = '',
        string $listName = '',
        array $options = [],
        array $contentOptions = []
    );

    public function updateContent(string $campaignId, string $html, array $options = []);

    /**
     * @return array|false
     */
    public function getLastError();

    public function lastActionSucceeded(): bool;
}
