# Manage newsletters in Laravel
[![Latest Version](https://img.shields.io/github/release/spatie/laravel-newsletter.svg?style=flat-square)](https://github.com/spatie/laravel-newsletter/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/spatie/laravel-newsletter/run-tests?label=tests)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-newsletter.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-newsletter)
[![StyleCI](https://styleci.io/repos/35035915/shield?branch=master)](https://styleci.io/repos/35035915)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-newsletter.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-newsletter)

This package provides an easy way to integrate MailChimp with Laravel 5 and 6. Behind the scenes v3 for the MailChimp API is used. Here are some examples of what you can do with the package:

> Please note the at the time of this writing the default merge variables in MailChimp are named `FNAME` and `LNAME`. In our examples we use `firstName` and `lastName` for extra readability. Make sure you rename those merge variables at MailChimp in order to make these examples work.

```php
// at the top of your class
use Newsletter;

// ...

Newsletter::subscribe('rincewind@discworld.com');

Newsletter::unsubscribe('the.luggage@discworld.com');

//Merge variables can be passed as the second argument
Newsletter::subscribe('sam.vines@discworld.com', ['FNAME'=>'Sam', 'LNAME'=>'Vines']);

//Subscribe someone to a specific list by using the third argument:
Newsletter::subscribe('nanny.ogg@discworld.com', ['FNAME'=>'Nanny', 'LNAME'=>'Ogg'], 'Name of your list');

//Subscribe someone to a specific list and require them to confirm via email:
Newsletter::subscribePending('nanny.ogg@discworld.com', ['FNAME'=>'Nanny', 'LNAME'=>'Ogg'], 'Name of your list');

//Subscribe or update someone
Newsletter::subscribeOrUpdate('sam.vines@discworld.com', ['FNAME'=>'Foo', 'LNAME'=>'Bar']);

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

// Get the tags for a member in a given list
Newsletter::getTags('lord.vetinari@discworld.com');

// Add tags for a member in a given list, any new tags will be created
Newsletter::addTags(['tag-1', 'tag-2'], 'lord.vetinari@discworld.com');

// Remove tags for a member in a given list
Newsletter::removeTags(['tag-1', 'tag-2'], 'lord.vetinari@discworld.com');

//If you want to do something else, you can get an instance of the underlying API:
Newsletter::getApi();
```

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

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
        ],
    ],

    /*
     * If you're having trouble with https connections, set this to false.
     */
    'ssl' => true,
];
```

## Support us

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us). 

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

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
