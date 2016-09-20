# Changelog

All Notable changes to `laravel-newsletter` will be documented in this file

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

