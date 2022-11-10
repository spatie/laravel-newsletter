<?php

use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\NullDriver;

afterEach(function () {
    Mockery::close();
});

it('can be call with any method', function () {
    $subject = new NullDriver();

    expect($subject)
        ->whatever()->toBeNull()
        ->subscription()->toBeNull()
        ->addTags('jason@testing.com', ['tags'])->toBeNull();
});

it('logs the method call when log is set', function () {
    $subject = new NullDriver(true);

    $log = \Mockery::mock();
    Log::swap($log);

    $log->shouldReceive('debug')->twice();

    expect($subject->whatever())
        ->and($subject->addTags('jason@testing.com', ['tags']))->toBeNull();

    $log->shouldHaveReceived('debug', ['Called Spatie Newsletter facade method: whatever with:', []]);
    $log->shouldHaveReceived('debug', ['Called Spatie Newsletter facade method: addTags with:', ['jason@testing.com', ['tags']]]);
});
