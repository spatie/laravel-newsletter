# Changelog

All notable changes to `laravel-newsletter` will be documented in this file.

## 5.1.0 - 2023-01-14

### What's Changed

- Laravel 10.x Compatibility by @laravel-shift in https://github.com/spatie/laravel-newsletter/pull/292

**Full Changelog**: https://github.com/spatie/laravel-newsletter/compare/5.0.0...5.1.0

## 5.0.0 - 2022-11-10

- the package is now driver based
- added driver from [Mailcoach](https://mailcoach.app) and [MailChimp](https://mailchimp.com)
- simplified code
- refactored tests to Pest

## 4.11.0 - 2022-01-14

- allow Laravel 9

## 4.10.0 - 2021-05-18

- add support for marketing permissions (#262)

## 4.9.0 - 2020-11-25

- add support for PHP 8.0 (#253)
- drop support for Laravel 5.8 (#253)

## 4.8.2 - 2020-09-30

- ensure the last action succeeded on `isSubscribed` (#244)

## 4.8.1 - 2020-09-09

- Add support for Laravel 8

## 4.8.0 - 2020-03-03

- add support for laravel 7

## 4.7.1 - 2019-09-16

- add support for laravel 6

## 4.7.0 - 2019-09-13

- Added: Ability to permanently delete list members

## 4.6.0 - 2019-09-04

- Added: Laravel 6.0 compatibility

## 4.5.0 - 2019-05-21

- add `null` driver

## 4.4.1 - 2019-04-28

- fix functions to manage tags

## 4.4.0 - 2019-04-24

- add functions to manage tags

## 4.3.0 - 2019-02-28

- drop support for L5.7 and below, PHP 7.1 and PHPUnit 7

## 4.2.3 - 2019-02-28

- Added: Laravel 5.8 compatibility

## 4.2.2 - 2018-09-04

- Added: Laravel 5.7 compatibility

## 4.2.1 - 2018-02-08

- Fixed: `unsubscribe` now returns a boolean when it fails

## 4.2.0 - 2018-02-08

- Added: Laravel 5.6 compatibility

## 4.1.0 - 2017-08-31

- Added: `subscribePending`

## 4.0.0 - 2017-08-30

- Added Laravel 5.5 compatibility
- Removed: Dropped support for older Laravel versions
- Changed: Renamed config file from `laravel-newsletter` to `newsletter`

## 3.7.0 - 2017-07-12

- Added: `isSubscribed`

## 3.6.1 - 2017-07-11

- Fixed: `lastActionSucceeded` check

## 3.6.0 - 2017-06-29

- Added: `getMembers` and `getMemberActivity`

## 3.5.0 - 2017-06-23

- Added: Method to update existing email address

## 3.4.0 - 2017-01-23

- Added: Support for Laravel 5.4

## 3.3.0 - 2016-11-24

- Added: Support for connection to MailChimp via http

## 3.2.0 - 2016-11-08

- Added: `subscribeOrUpdate` method

## 3.1.0 - 2016-10-17

- Added: `delete` method

## 3.0.6 - 2016-10-13

- Fixed: When unsubcribing a user the status of that user will be set to `unsubscribed` instead of downright deleting the user

## 3.0.5 - 2016-09-20

- Fixed: Fail when calling `hasMember` multiple times

## 3.0.4 - 2016-08-24

- Added: L5.3 compatiblity

## 3.0.3 - 2016-07-11

- Fixed: E-mail adresses with capitals

## 3.0.2 - 2016-04-22

- Fixed: Make has `hasMember` more robust

## 3.0.1 - 2016-04-22

- Fixed: `hasMember` bug

## 3.0.0 - 2016-04-22

- Changed: Complete rewrite
- Changed: Under the hood v3 of the MailChimp API is used

## 2.2.0

- Added: Compatibility for Laravel 5.2

## 2.1.0

- Added: Dunctions to create, update, delete campaigns, update subscriber details

## 2.0.0

- Changed: Merge vars can now be specified in the subscribe function.

## 1.1.0

- Added: Various configuration options

## 1.0.3

- Fixed: Binding of the `NewsletterInterface` in the service provider

## 1.0.2

- Changed: `getenv()` to Laravel's `env()` in the config file

## 1.0.1

- Added: Missing binding to the service provider

## 1.0.0

- Initial release
