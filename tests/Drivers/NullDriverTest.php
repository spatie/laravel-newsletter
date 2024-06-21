<?php

use Spatie\Newsletter\Drivers\NullDriver;

it('uses the NullDriver with null config value', function () {
    config()->set('newsletter.driver', null);

    expect(app('newsletter'))->toBeInstanceOf(NullDriver::class);
});

it('uses the NullDriver with log config value', function () {
    config()->set('newsletter.driver', 'log');

    expect(app('newsletter'))->toBeInstanceOf(NullDriver::class);
});

it('uses the NullDriver with NullDriver::class config', function () {
    config()->set('newsletter.driver', NullDriver::class);

    expect(app('newsletter'))->toBeInstanceOf(NullDriver::class);
});

it('accepts calls to the NullDriver', function () {
    config()->set('newsletter.driver', null);

    expect(app('newsletter')->subscribe('example@example.org'))->toBeNull();
});
