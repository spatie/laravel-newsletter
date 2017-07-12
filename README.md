# Manage newsletters in Laravel 5
[![Latest Version](https://img.shields.io/github/release/spatie/laravel-newsletter.svg?style=flat-square)](https://github.com/spatie/laravel-newsletter/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-newsletter/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-newsletter)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-newsletter.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-newsletter)
[![StyleCI](https://styleci.io/repos/35035915/shield?branch=master)](https://styleci.io/repos/35035915)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-newsletter.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-newsletter)

This package provides an easy way to integrate MailChimp with Laravel 5. Behind the scenes v3 for the MailChimp API is used. Here are some examples of what you can do with the package:

> Please note the at the time of this writing the default merge variables in MailChimp are named `FNAME` and `LNAME`. In our examples we use `firstName` and `lastName` for extra readability.

```php
Newsletter::subscribe('rincewind@discworld.com');

Newsletter::unsubscribe('the.luggage@discworld.com');

//Merge variables can be passed as the second argument
Newsletter::subscribe('sam.vines@discworld.com', ['firstName'=>'Sam', 'lastName'=>'Vines']);

//Subscribe someone to a specific list by using the third argument:
Newsletter::subscribe('nanny.ogg@discworld.com', ['firstName'=>'Nanny', 'lastName'=>'Ogg'], 'Name of your list');

//Subscribe or update someone
Newsletter::subscribeOrUpdate('sam.vines@discworld.com', ['firstName'=>'Foo', 'lastName'=>'Bar']);

// Change the email address of an existing subscriber
Newsletter::updateEmailAddress('rincewind@discworld.com', 'the.luggage@discworld.com');

//Get some member info, returns an array described in the official docs
Newsletter::getMember('lord.vetinari@discworld.com');

//Get the member activity, returns an array with recent activity for a given user
Newsletter::getMemberActivity('lord.vetinari@discworld.com');

//Get the members for a given list, optionally filtered by passing a second array of parameters
Newsletter::getMembers();

//Check if a member is subscribed to a list
Newsletter::isSubscribed('rincewind@discworld.com');

//Returns a boolean
Newsletter::hasMember('greebo@discworld.com');

//If you want to do something else, you can get an instance of the underlying API:
Newsletter::getApi();
```

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we appreciated you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

All postcards are published [on our website](https://spatie.be/en/opensource/laravel).

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
    Spatie\Newsletter\NewsletterServiceProvider::class,
    ...
];
```

If you want to make use of the facade you must install it as well.

```php
// config/app.php
'aliases' => [
    ..
    'Newsletter' => Spatie\Newsletter\NewsletterFacade::class,
];
```

To publish the config file to `app/config/laravel-newsletter.php` run:

```bash
php artisan vendor:publish --provider="Spatie\Newsletter\NewsletterServiceProvider"
```

This will publish a file `laravel-newsletter.php` in your config directory with the following contents:
```php
return [

        /*
         * The api key of a MailChimp account. You can find yours here:
         * https://us10.admin.mailchimp.com/account/api-key-popup/
         */
        'apiKey' => env('MAILCHIMP_APIKEY'),

        /*
         * When not specifying a listname in the various methods,
         *  this list name will be used.
         */
        'defaultListName' => 'subscribers',

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
                 'id' => env('MAILCHIMP_LIST_ID'),
            ],
        ],
];
```

## Usage

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
Newsletter::subscribe('rincewind@discworld.com', ['firstName'=>'Rince', 'lastName'=>'Wind']);
```
> Please note the at the time of this writing the default merge variables in MailChimp are named `FNAME` and `LNAME`. In our examples we use `firstName` and `lastName` for extra readability.

You can subscribe someone to a specific list by using the third argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['firstName'=>'Rince', 'lastName'=>'Wind'], 'subscribers');
```
That third argument is the name of a list you configured in the config file.

You can also subscribe and/or update someone. The person will be subscribed or updated if he/she is already subscribed:

 ```php
 Newsletter::subscribeOrUpdate('rincewind@discworld.com', ['firstName'=>'Foo', 'lastname'=>'Bar']);
 ```
 
You can subscribe someone to one or more specific group(s)/interest(s) by using the fourth argument:

```php
Newsletter::subscribeOrUpdate('rincewind@dscworld.com', ['firstName'=>'Rince','lastName'=>'Wind'], 'subscribers', ['interests'=>['interestId'=>true, 'interestId'=>true]])
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

### Getting subscriber info

You can get information on a subscriber by using the `getMember` function:
```php
Newsletter::getMember('lord.vetinari@discworld.com');
```

This will return an array with information on the subscriber. If there's no one subscribed with that
e-mail address the function will return `false`

There's also a convenience method to check if someone is already subscribed:

```php
Newsletter::hasMember('nanny.ogg@discworld.com'); //returns a bool
```

In addition to this you can also check if a user is subscribed to your list:

```php
Newsletter::isSubscribed('lord.vetinari@discworld.com'); //returns a bool
```

### Creating a campaign

This is how you create a campaign:
```php
/**
 * @param string $fromName
 * @param string $replyTo
 * @param string $subject
 * @param string $html
 * @param string $listName
 * @param array  $options
 * @param array  $contentOptions
 *
 * @return array|bool
 *
 * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
 */
public function createCampaign($fromName, $replyTo, $subject, $html = '', $listName = '', $options = [], $contentOptions = [])
```

Note the campaign will only be created, no mails will be sent out.

### Handling errors

If something went wrong you can get the last error with:
```php
Newsletter::getLastError();
```

If you just want to make sure if the last action succeeded you can use:
```php
Newsletter::lastActionSucceeded(); //returns a bool
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

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [freek@spatie.be](mailto:freek@spatie.be) instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
