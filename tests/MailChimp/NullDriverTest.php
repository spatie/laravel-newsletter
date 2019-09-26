<?php

namespace Spatie\Newsletter\Test;

use Spatie\Newsletter\NullDriver;
use Illuminate\Support\Facades\Log;

class NullDriverTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function it_logs_the_method_call_when_log_is_set()
    {
        $subject = new NullDriver(true);

        $log = \Mockery::mock();
        Log::swap($log);

        $log->shouldReceive('debug')->twice();

        $this->assertNull($subject->unsubscribe('jason@testing.com', 'test list'));
        $this->assertNull($subject->addTags(['tags'], 'jason@testing.com'));

        $log->shouldHaveReceived(
            'debug', ['Called Spatie Newsletter facade method: unsubscribe with:', ['jason@testing.com', 'test list']]
        );
        $log->shouldHaveReceived(
            'debug',
            ['Called Spatie Newsletter facade method: addTags with:', [['tags'], 'jason@testing.com']]
        );
    }
}
