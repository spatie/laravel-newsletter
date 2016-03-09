# Manage newsletters in Laravel 5
[![Latest Version](https://img.shields.io/github/release/spatie/laravel-newsletter.svg?style=flat-square)](https://github.com/spatie/laravel-newsletter/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-newsletter/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-newsletter)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/10993a65-449a-488a-886c-f810b9950070/mini.png)](https://insight.sensiolabs.com/projects/10993a65-449a-488a-886c-f810b9950070)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-newsletter.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-newsletter)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-newsletter.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-newsletter)

This package provides an easy way to integrate email marketing services with Laravel 5. Currently the package only supports MailChimp. In the future more services may get added.

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Installation

You can install this package via composer using:

```bash
composer require spatie/laravel-newsletter
```

You must also install this service provider.

```php
// config/app.php
'providers' => [
    ...
    'Spatie\Newsletter\NewsletterServiceProvider',
    ...
];
```

If you want to make use of the facade you must install it as well.

```php
// config/app.php
'aliases' => [
    ...
    'Newsletter' => 'Spatie\Newsletter\NewsletterFacade',
];
```

To publish the config file to ``app/config/laravel-newsletter.php`` run:

```bash
php artisan vendor:publish --provider="Spatie\Newsletter\NewsletterServiceProvider"
```

This wil publish a file `laravel-newsletter.php` in your config directory with the following contents: 
```php
return [

    'mailChimp' => [

        /*
         * The api key of a MailChimp account. You can find yours here:
         * https://us10.admin.mailchimp.com/account/api-key-popup/
         */
        'apiKey' => env('MAILCHIMP_APIKEY'),

        /*
         * Here you can define properties of the lists you want to
         * send campaigns.
         */
        'lists' => [

            /*
             * This key is used to identify this list. It can be used
             * in the various methods provided by this package.
             *
             * You can set it to any string you want and you can add
             * as many lists as you want.
             */
            'subscribers' => [

                /*
                 * A mail chimp list id. Check the mailchimp docs if you don't know
                 * how to get this value:
                 * http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id
                 */
                'id' => '',

                /*
                 * These values will be used when creating a new campaign.
                 */
                'createCampaign' => [
                    'fromEmail' => '',
                    'fromName' => '',
                    'toName' => ''
                ],
                
                /*
                 * These values will be used when subscribing to a list.
                 */
                'subscribe' => [
                    'emailType' => 'html',
                    'requireDoubleOptin' => false,
                    'updateExistingUser' => false
                ],
                
                /*
                 * These values will be used when unsubscribing from a list.
                 */
                'unsubscribe' => [
                    'deletePermanently' => false,
                    'sendGoodbyeEmail' => false,
                    'sendUnsubscribeEmail' => false
                ],
            ],
        ],
    ],
];

```

## Upgrading

### From 1.0 to 2.0
If you specified a listName on a subscription you need to modify that call from
`Newsletter::subscribe($email, $listName)`
to
`Newsletter::subscribe($email, [], $listName)`

## Usage

After you've installed the package and filled in the values in the config-file working with this package will be a breeze. All the following examples use the facade. Don't forget to import it at the top of your file.

```php
use Newsletter;
```

Subscribing an email address can be done like this:

```php
use Newsletter;

Newsletter::subscribe('rincewind@discworld.com');
```

If you need to update the subscriber info

```php
/**
 * Update a member subscribed to a list
 *
 * @param $email
 * @param array $mergeVars
 * @param string $list
 *
 * @return mixed
 */
Newsletter::updateMember($email, $mergeVars = [],  $list = '');
```

Let's unsubcribe someone:

```php
Newsletter::unsubscribe('the.luggage@discworld.com');
```

You can pass some merge variables as the second argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['firstName'=>'Rince', 'lastName'=>'Wind']);
```
Please note the at the time of this writing the default merge variables in MailChimp are named `FNAME` and `LNAME`. In our examples we use `firstName` and `lastName` for extra readability.

You can subscribe someone to a specific list by using the third argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['firstName'=>'Rince', 'lastName'=>'Wind'], 'subscribers');
```
That third argument is the name of a list you configured in the config file.


This is how you create a campaign:

```php
$subject = 'The Truth newsletter';
$contents = '<h1>Big news</h1>The world is carried by four elephants on a turtle!';

Newsletter::createCampaign($subject, $contents);
```
The method will create a campaign, but not send it. If you want to send a campaign, see below.

If you have multiple lists defined in the config file you must pass the name of the list an extra parameter:

```php
Newsletter::subscribe('havelock.vetinari@discworld.com', ['firstName'=>'Havelock', 'lastName'=>'Vetinari'], 'mySecondList');
Newsletter::unsubscribe('sam.vimes@discworld.com', ['firstName'=>'Sam', 'lastName'=>'Vines'], 'mySecondList');

Newsletter::createCampaign($subject, $contents, 'mySecondList');
```

And this is how to update a campaign:

```php
/**
 * Update a newsletter campaign.
 *
 * @param $campaignId string
 * @param $fieldName string
 * @param $value array
 *
 * @return mixed
 */
Newsletter::updateCampaign($campaignId, $fieldName, $value = []);
```

Example of how to update the content or the subject of a campaign

```php
Newsletter::updateCampaign(
    $campaignId,
    'content', 
    [
        'html' => File::get( 'path/to/some/rendered/view/file' ),
    ]
);

Newsletter::updateCampaign(
    $campaignId,
    'options', 
    [
        'subject' => 'New subject'
    ]
);
```

You can use this method to send a test campaign ...

```php
/**
 * Send a test newsletter campaign.
 *
 * @param $campaignId string
 * @param $emails string or array
 * @param $sendType string
 *
 * @return mixed
 */
Newsletter::sendTestCampaign($campaignId, $emails, $sendType = '')
```

... or send the final campaign

```php
/**
 * Send a newsletter campaign.
 *
 * @param $campaignId string
 *
 * @return mixed
 */
Newsletter::sendCampaign($campaignId)
```


And finally, this is how you delete a campaign:

```php
/**
 * Delete a newsletter campaign.
 *
 * @param $campaignId
 *
 * @return mixed
 */
Newsletter::deleteCampaign($campaignId);
```

If you need more functionality you get an instance of the underlying service api with:

```
$api = Newsletter::getApi();
```

As this package currently only supports MailChimp this method will always return an instance of [the MailChimp API](https://bitbucket.org/mailchimp/mailchimp-api-php).


## Testing

Run the tests with:
```bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [freek@spatie.be](mailto:freek@spatie.be) instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

This package was inspired by the [Bulk Email Notifications series on Laracasts](https://laracasts.com/lessons/bulk-email-notifications-part-1).

## About Spatie
Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

