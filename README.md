# Manage newsletters in Laravel
[![Latest Version](https://img.shields.io/github/release/spatie/laravel-newsletter.svg?style=flat-square)](https://github.com/spatie/laravel-newsletter/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/spatie/laravel-newsletter/run-tests?label=tests)
![Check & fix styling](https://github.com/spatie/laravel-newsletter/workflows/Check%20&%20fix%20styling/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-newsletter.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-newsletter)

This package provides an easy way to integrate MailChimp with Laravel.

Should you find that Mailchimp is too expensive for your use case, consider using [Mailcoach](https://mailcoach.app) instead. Mailcoach is a premium Laravel package that allows you to self host your email lists and campaigns.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-newsletter.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-newsletter)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install this package via composer using:

```bash
composer require spatie/laravel-newsletter
```

The package will automatically register itself.

To publish the config file to `config/newsletter.php` run:

```bash
php artisan vendor:publish --provider="Spatie\Newsletter\NewsletterServiceProvider"
```

This will publish a file `newsletter.php` in your config directory with the following contents:
```php
return [

    /*
     * The driver to use to interact with MailChimp API.
     * You may use "log" or "null" to prevent calling the
     * API directly from your environment.
     */
    'driver' => env('MAILCHIMP_DRIVER', 'api'),

    /*
     * The API key of a MailChimp account. You can find yours at
     * https://us10.admin.mailchimp.com/account/api-key-popup/.
     */
    'apiKey' => env('MAILCHIMP_APIKEY'),

    /*
     * The listName to use when no listName has been specified in a method.
     */
    'defaultListName' => 'subscribers',

    /*
     * Here you can define properties of the lists.
     */
    'lists' => [

        /*
         * This key is used to identify this list. It can be used
         * as the listName parameter provided in the various methods.
         *
         * You can set it to any string you want and you can add
         * as many lists as you want.
         */
        'subscribers' => [

            /*
             * A MailChimp list id. Check the MailChimp docs if you don't know
             * how to get this value:
             * http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id.
             */
            'id' => env('MAILCHIMP_LIST_ID'),

            /*
             * The GDPR marketing permissions of this audience.
             * You can get a list of your permissions with this command: "php artisan newsletter:permissions"
             */
            'marketing_permissions' => [
                // 'email' => '2a4819ebc7',
                // 'customized_online_advertising' => '4256fc7dc5',
            ],

        ],
    ],

    /*
     * If you're having trouble with https connections, set this to false.
     */
    'ssl' => true,

];
```

## Usage

Behind the scenes v3 for the MailChimp API is used.

After you've installed the package and filled in the values in the config-file working with this package will be a breeze. All the following examples use the facade. Don't forget to import it at the top of your file.

```php
use Newsletter;
```

### Subscribing, updating and unsubscribing

Subscribing an email address can be done like this:

```php
use Newsletter;

Newsletter::subscribe('rincewind@discworld.com');
```

Let's unsubscribe someone:

```php
Newsletter::unsubscribe('the.luggage@discworld.com');
```

You can pass some merge variables as the second argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['FNAME'=>'Rince', 'LNAME'=>'Wind']);
```
> Please note the at the time of this writing the default merge variables in MailChimp are named `FNAME` and `LNAME`. In our examples we use `firstName` and `lastName` for extra readability.

You can subscribe someone to a specific list by using the third argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['FNAME'=>'Rince', 'LNAME'=>'Wind'], 'subscribers');
```
That third argument is the name of a list you configured in the config file.

You can also subscribe and/or update someone. The person will be subscribed or updated if he/she is already subscribed:

 ```php
 Newsletter::subscribeOrUpdate('rincewind@discworld.com', ['FNAME'=>'Foo', 'LNAME'=>'Bar']);
 ```

You can subscribe someone to one or more specific group(s)/interest(s) by using the fourth argument:

```php
Newsletter::subscribeOrUpdate('rincewind@dscworld.com', ['FNAME'=>'Rince','LNAME'=>'Wind'], 'subscribers', ['interests'=>['interestId'=>true, 'interestId'=>true]])
```
Simply add `false` if you want to remove someone from a group/interest.

You can also unsubscribe someone from a specific list:
```php
Newsletter::unsubscribe('rincewind@discworld.com', 'subscribers');
```

### Deleting subscribers

Deleting is not the same as unsubscribing. Unlike unsubscribing, deleting a member will result in the loss of all history (add/opt-in/edits) as well as removing them from the list. In most cases you want to use `unsubscribe` instead of `delete`.

Here's how to perform a delete:

```php
Newsletter::delete('rincewind@discworld.com');
```

### Deleting subscribers permanently

Delete all personally identifiable information related to a list member, and remove them from a list. This will make it impossible to re-import the list member.

Here's how to perform a permanent delete:

```php
Newsletter::deletePermanently('rincewind@discworld.com');
```

### Getting subscriber info

You can get information on a subscriber by using the `getMember` function:
```php
Newsletter::getMember('lord.vetinari@discworld.com');
```

This will return an array with information on the subscriber. If there's no one subscribed with that
e-mail address the function will return `false`

There's also a convenience method to check if someone is already subscribed:

```php
Newsletter::hasMember('nanny.ogg@discworld.com'); //returns a boolean
```

In addition to this you can also check if a user is subscribed to your list:

```php
Newsletter::isSubscribed('lord.vetinari@discworld.com'); //returns a boolean
```

### Creating a campaign

This the signature of `createCampaign`:
```php
public function createCampaign(
    string $fromName,
    string $replyTo,
    string $subject,
    string $html = '',
    string $listName = '',
    array $options = [],
    array $contentOptions = [])
```

Note the campaign will only be created, no emails will be sent out.

### Working with GDRP marketing permissions
If you are subject to GDRP, you need to [collect your user's consent](https://mailchimp.com/help/collect-consent-with-gdpr-forms/). This package provides a simple artisan command that outputs a nice table with the names and ID's of your audience's marketing permissions.

Get the marketing permissions of your default list:
```bash
php artisan newsletter:permissions
```

You may also get the permissions of a specific list:
 ```bash
php artisan newsletter:permissions subscribers
```

Next, you need to add the permissions to your list's config:
```php
'lists' => [

    'subscribers' => [

        'id' => env('MAILCHIMP_LIST_ID'),

        'marketing_permissions' => [
            'email' => '2a4819ebc7',
            'customized_online_advertising' => '4256fc7dc5',
        ],

    ],
],
```

Now you can easily update a subscriber's marketing permissions. The first argument is the email, the second argument the permission key from the config, the third argument a boolean to enable/disable the permission, and an optional fourth argument is the name of a specific list.

Update a subscriber's marketing permission:
```php
Newsletter::setMarketingPermission('rincewind@discworld.com', 'email', true);
```

### Handling errors

If something went wrong you can get the last error with:
```php
Newsletter::getLastError();
```

If you just want to make sure if the last action succeeded you can use:
```php
Newsletter::lastActionSucceeded(); //returns a boolean
```

### Need something else?

If you need more functionality you get an instance of the underlying [MailChimp Api](https://github.com/drewm/mailchimp-api) with:

```php
$api = Newsletter::getApi();
```

## Testing

Run the tests with:
```bash
vendor/bin/phpunit
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [freek@spatie.be](mailto:freek@spatie.be) instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)
be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
