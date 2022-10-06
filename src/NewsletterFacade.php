<?php

namespace Spatie\Newsletter;

use Illuminate\Support\Facades\Facade;

/**
 * Newsletter Facade
 *
 * @method static array|bool subscribe(string $email, array $mergeFields = [], string $listName = '', array $options = [])
 * @method static array|bool subscribePending(string $email, array $mergeFields = [], string $listName = '', array $options = [])
 * @method static array|bool subscribeOrUpdate(string $email, array $mergeFields = [], string $listName = '', array $options = [])
 * @method static array|bool getMembers(string $listName = '', array $parameters = [])
 * @method static array|bool getMember(string $email, string $listName = '')
 * @method static array|bool getMemberActivity(string $email, string $listName = '')
 * @method static bool hasMember(string $email, string $listName = '')
 * @method static bool isSubscribed(string $email, string $listName = '')
 * @method static array|bool unsubscribe(string $email, string $listName = '')
 * @method static array|bool updateEmailAddress(string $currentEmailAddress, string $newEmailAddress, string $listName = '')
 * @method static array|bool delete(string $email, string $listName = '')
 * @method static array|bool deletePermanently(string $email, string $listName = '')
 * @method static array|bool getTags(string $email, string $listName = '')
 * @method static array|bool addTags(array $tags, string $email, string $listName = '')
 * @method static array|bool removeTags(array $tags, string $email, string $listName = '')
 * @method static array|bool createCampaign(string $fromName, string $replyTo, string $subject, string $html = '', string $listName = '', array $options = [], array $contentOptions = [])
 * @method static array|bool updateContent(string $campaignId, string $html, array $options = [])
 * @method static \DrewM\MailChimp\MailChimp getApi()
 * @method static string|false getLastError()
 * @method static bool lastActionSucceeded()
 * @method static array|false getMarketingPermissions(string $listName = '')
 * @method static array|false setMarketingPermission(string $email, string $permission, bool $bool, string $listName = '')
 *
 * @package Spatie\Newsletter
 */
class NewsletterFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'newsletter';
    }
}
