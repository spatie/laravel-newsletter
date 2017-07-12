# Changelog

All notable changes to `laravel-newsletter` will be documented in this file

## 3.7.0 - 2017-07-12

- add `isSubscribed`

## 3.6.1 - 2017-07-11

- fix `lastActionSucceeded` check

## 3.6.0 - 2017-06-29

- add `getMembers` and `getMemberActivity`

## 3.5.0 - 2017-06-23

- add method to update existing email address

## 3.4.0 - 2017-01-23

- add support for Laravel 5.4

## 3.3.0 - 2016-11-24

- add support for connection to MailChimp via http

## 3.2.0 - 2016-11-08

- add `subscribeOrUpdate` method

## 3.1.0 - 2016-10-17

- add `delete` method

## 3.0.6 - 2016-10-13

- when unsubcribing a user the status of that user will be set to `unsubscribed` instead of downright deleting the user

## 3.0.5 - 2016-09-20

- fix for fail when calling `hasMember` multiple times

## 3.0.4 - 2016-08-24

- add L5.3 compatiblity 

## 3.0.3 - 2016-07-11
- improvements on handling of emailadresses with capitals

## 3.0.2 - 2016-04-22
- make has `hasMember` more robust

## 3.0.1 - 2016-04-22
- fixed the `hasMember` function

##3.0.0 - 2016-04-22
- complete rewrite
- under the hood v3 of the MailChimp API is used

##2.2.0
- add compatibility for Laravel 5.2

##2.1.0
- added functions to create, update, delete campaigns, update subscriber details

##2.0.0
- merge vars can now be specified in the subscribe function.

##1.1.0
- added various configuration options

##1.0.3
- fixed binding of the newsletterinterface in the service provider

##1.0.2
- changed getenv() to Laravel's env() in the config file

##1.0.1
- added a missing binding to the service provider

##1.0.0

- initial release

