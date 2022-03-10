<?php

namespace Spatie\Newsletter\Test;

use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\NullDriver;

afterEach(function () {
    \Mockery::close();
});

it('can be call with any method', function () {
    $subject = new NullDriver();

    $this->assertNull($subject->whatever());
    $this->assertNull($subject->subscription());
    $this->assertNull($subject->addTags('jason@testing.com', ['tags']));
});

it('logs the method call when log is set', function () {
    $subject = new NullDriver(true);

    $log = \Mockery::mock();
    Log::swap($log);

    $log->shouldReceive('debug')->twice();

    $this->assertNull($subject->whatever());
    $this->assertNull($subject->addTags('jason@testing.com', ['tags']));

    $log->shouldHaveReceived('debug', ['Called Spatie Newsletter facade method: whatever with:', []]);
    $log->shouldHaveReceived('debug', ['Called Spatie Newsletter facade method: addTags with:', ['jason@testing.com', ['tags']]]);
});
