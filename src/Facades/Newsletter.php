<?php

namespace Spatie\Newsletter\Facades;

use Spatie\MailcoachSdk\Mailcoach;
use \DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\Facade;

/**
 * Newsletter Facade
 *
 * @method static array|bool subscribe(string $email, array $mergeFields = [], string $listName = '', array $options = [])
 * @method static array|bool subscribeOrUpdate(string $email, array $mergeFields = [], string $listName = '', array $options = [])
 * @method static array|bool getMember(string $email, string $listName = '')
 * @method static bool hasMember(string $email, string $listName = '')
 * @method static bool isSubscribed(string $email, string $listName = '')
 * @method static array|bool unsubscribe(string $email, string $listName = '')
 * @method static array|bool delete(string $email, string $listName = '')
 * @method static Mailcoach|MailChimp getApi()
 *
 * @package Spatie\Newsletter
 */
class Newsletter extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'newsletter';
    }
}
