<div align="left">
    <a href="https://spatie.be/open-source?utm_source=github&utm_medium=banner&utm_campaign=laravel-newsletter">
      <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://spatie.be/packages/header/laravel-newsletter/html/dark.webp">
        <img alt="Logo for laravel-newsletter" src="https://spatie.be/packages/header/laravel-newsletter/html/light.webp">
      </picture>
    </a>

<h1>Manage newsletters in Laravel</h1>

[![Latest Version](https://img.shields.io/github/release/spatie/laravel-newsletter.svg?style=flat-square)](https://github.com/spatie/laravel-newsletter/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![run-tests](https://github.com/spatie/laravel-newsletter/actions/workflows/run-tests.yml/badge.svg)](https://github.com/spatie/laravel-newsletter/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/spatie/laravel-newsletter/actions/workflows/phpstan.yml/badge.svg)](https://github.com/spatie/laravel-newsletter/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-newsletter.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-newsletter)
    
</div>

This package provides an easy way to integrate subscriptions to email lists of various email services.

Currently this package support:

- [Mailcoach](https://mailcoach.app) (built by us :-))
- [MailChimp](https://mailchimp.com)
- [MailerLite](https://mailerlite.com)
- [Brevo](https://brevo.com)

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-newsletter.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-newsletter)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

This package requires PHP 8.2 or higher and Laravel 12 or 13. Laravel 13 requires PHP 8.3 or higher.

You can install this package via Composer using:

```bash
composer require spatie/laravel-newsletter
```

To publish the config file to `config/newsletter.php` run:

```bash
php artisan vendor:publish --tag="newsletter-config"
```

This will publish a file `newsletter.php` in your config directory with the following contents:

```php
return [

    /*
     * The driver to use to interact with MailChimp API.
     * You may use "log" or "null" to prevent calling the
     * API directly from your environment.
     */
    'driver' => env('NEWSLETTER_DRIVER', Spatie\Newsletter\Drivers\MailcoachDriver::class),

    /**
     * These arguments will be given to the driver.
     */
    'driver_arguments' => [
        'api_key' => env('NEWSLETTER_API_KEY'),

        'endpoint' => env('NEWSLETTER_ENDPOINT'),
    ],

    /*
     * The list name to use when no list name is specified in a method.
     */
    'default_list_name' => 'subscribers',

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
             * When using the Mailcoach driver, this should be the Email list UUID
             * which is displayed in the Mailcoach UI
             *
             * When using the MailChimp driver, this should be a MailChimp list id.
             * http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id.
             *
             * When using the MailerLite driver, this should be a MailerLite group id.
             *
             * When using the Brevo driver, this should be a Brevo list id (integer).
             */
            'id' => env('NEWSLETTER_LIST_ID'),
        ],
    ],
];
```

### Using Mailcoach

To let this package work with Mailcoach, you need to install the Mailcoach SDK.

```bash
composer require spatie/mailcoach-sdk-php
```

Next, you must provide values for the API key, endpoint and `list.subscribers.id` in the config file. You'll find the API key and endpoint in the [Mailcoach](https://mailcoach.app) settings screen. The value for `list.subscribers.id` must be the UUID of an email list on Mailcoach. You'll find this value on the settings screen of an email list

### Using MailChimp

To use MailChimp, install this extra package.

```bash
composer require drewm/mailchimp-api
```

The `driver` key of the `newsletter` config file must be set to `Spatie\Newsletter\Drivers\MailChimpDriver::class`.

Next, you must provide values for the API key and `list.subscribers.id`. You'll find these values in the MailChimp UI.

The `endpoint` config value must be set to null.

### Using MailerLite

To use MailerLite, install this extra package.

```bash
composer require mailerlite/mailerlite-php
```

The `driver` key of the `newsletter` config file must be set to `Spatie\Newsletter\Drivers\MailerLiteDriver::class`.

You need to provide the API key and the `group.id`. These can be found in your MailerLite Dashboard under Integrations > API.

The `endpoint` config value must be set to null.

### Using Brevo

To use Brevo, install this extra package.

```bash
composer require "getbrevo/brevo-php:^5.0"
```

The `driver` key of the `newsletter` config file must be set to `Spatie\Newsletter\Drivers\BrevoDriver::class`.

Set `newsletter.driver_arguments.api_key` using `NEWSLETTER_API_KEY`. The API key can be found in your Brevo dashboard under SMTP & API > API Keys. Set `newsletter.lists.subscribers.id` using `NEWSLETTER_LIST_ID` to the positive integer id of a contact list under Contacts > Lists. Numeric strings from your `.env` file are supported.

The `endpoint` config value is unused by this driver and can remain null. Optional Brevo SDK settings, such as `timeout` or a PSR-18 `client`, can be passed through `newsletter.driver_arguments.options`.

Pass existing Brevo contact attributes, using their uppercase names, as the second argument:

```php
Newsletter::subscribeOrUpdate('jane@example.com', ['FIRSTNAME' => 'Jane'], 'subscribers');
```

`subscribe` creates a contact and fails if it already exists. `subscribeOrUpdate` also updates existing contacts. The fourth argument accepts additional Brevo create-contact fields, such as `smsBlacklisted`; it cannot override the email, attributes, selected list, or create/update mode supplied by the method. Existing email blocklisting is not automatically cleared.

`getMember`, `hasMember`, and `isSubscribed` use the selected list, or `default_list_name` when omitted. `isSubscribed` also checks email blocklisting and list-specific unsubscriptions. `getMember` returns an array containing the contact's attributes, or `false` when the contact is not in that list.

`unsubscribe` removes the contact from only the selected (or default) list. It does not globally blocklist or delete the contact. **`delete` permanently deletes the contact across the Brevo account, regardless of the list name.** Use `unsubscribe` to remove only list membership.

Failed API requests return `false`. Use `Newsletter::getLastError()` and `Newsletter::lastActionSucceeded()` to inspect errors. Subscribe, update, and unsubscribe return the refreshed contact; if that follow-up lookup fails, they return `false` even though the preceding mutation may already have succeeded.

## Usage

After you've installed the package and filled in the values in the config-file working with this package will be a breeze. All the following examples use the facade. Don't forget to import it at the top of your file.

```php
use Spatie\Newsletter\Facades\Newsletter;
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

For Mailcoach, you can pass extra attributes as the second argument:

```php
Newsletter::subscribe('rincewind@discworld.com', ['first_name' => 'Rince', 'last_name' => 'Wind']);
```

For MailChimp you can pass merge variables as the second argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['FNAME'=>'Rince', 'LNAME'=>'Wind']);
```

For MailerLite you can pass subscriber fields as the second argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['name'=>'Rince', 'last_name'=>'Wind']);
```

You can subscribe someone to a specific list by passing a list name:
```php
Newsletter::subscribe('rincewind@discworld.com', listName: 'subscribers');
```

That third argument is the name of a list you configured in the config file.

You can also subscribe and/or update someone. The person will be subscribed or updated if he/she is already subscribed:

 ```php
 Newsletter::subscribeOrUpdate('rincewind@discworld.com', ['first_name' => 'Rince', 'last_name' => 'Wind']);
 ```

For MailChimp, You can subscribe someone to one or more specific group(s)/interest(s) by using the fourth argument:

```php
Newsletter::subscribeOrUpdate(
   'rincewind@dscworld.com', 
   ['FNAME'=>'Rince','LNAME'=>'Wind'], 
   'subscribers', 
   ['interests'=>['interestId'=>true, 'interestId'=>true]],
);
```

Simply add `false` if you want to remove someone from a group/interest.

Here's how to unsubscribe someone from a specific list:

```php
Newsletter::unsubscribe('rincewind@discworld.com', 'subscribers');
```

For MailerLite, passing a list name as the second argument removes the subscriber from the matching group. Without a list name, the subscriber is marked as unsubscribed across the account.

### Deleting subscribers

Deleting is not the same as unsubscribing. Unlike unsubscribing, deleting a member will result in the loss of all history (add/opt-in/edits) as well as removing them from the list. In most cases, you want to use `unsubscribe` instead of `delete`.

Here's how to perform a delete:

```php
Newsletter::delete('rincewind@discworld.com');
```

### Getting subscriber info

You can get information on a subscriber by using the `getMember` function:
```php
Newsletter::getMember('lord.vetinari@discworld.com');
```

For MailCoach, this will return an instance of `Spatie\Mailcoach\Resources|Subscriber`
For MailChimp, this will return an array with information on the subscriber. 

If there's no one subscribed with that e-mail address the function will return `false`

There's also a convenient method to check if someone is already subscribed:

```php
Newsletter::hasMember('nanny.ogg@discworld.com'); //returns a boolean
```

In addition to this, you can also check if a user is subscribed to your list:

```php
Newsletter::isSubscribed('lord.vetinari@discworld.com'); //returns a boolean
```

### Need something else?

If you need more functionality you get an instance of the underlying API with

```php
$api = Newsletter::getApi();
```

If you're having trouble getting the MailChimp integration, you can see the last error with:

```php
Newsletter::getApi()->getLastError();
```

## Testing

Run the tests with:

```bash
vendor/bin/pest
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)
be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
