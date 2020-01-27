<?php

namespace Spatie\Newsletter\Test;

use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\NullDriver;

class NullDriverTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function it_can_be_call_with_any_method()
    {
        $subject = new NullDriver();

        $this->assertNull($subject->whatever());
        $this->assertNull($subject->subscription());
        $this->assertNull($subject->addTags('jason@testing.com', ['tags']));
    }

    /** @test */
    public function it_logs_the_method_call_when_log_is_set()
    {
        $subject = new NullDriver(true);

        $log = \Mockery::mock();
        Log::swap($log);

        $log->shouldReceive('debug')->twice();

        $this->assertNull($subject->whatever());
        $this->assertNull($subject->addTags('jason@testing.com', ['tags']));

        $log->shouldHaveReceived('debug', ['Called Spatie Newsletter facade method: whatever with:', []]);
        $log->shouldHaveReceived('debug', ['Called Spatie Newsletter facade method: addTags with:', ['jason@testing.com', ['tags']]]);
    }
}
