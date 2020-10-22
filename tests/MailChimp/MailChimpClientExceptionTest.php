<?php

namespace Spatie\Newsletter\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Newsletter\Exceptions\MailChimpClientException;

class MailChimpClientExceptionTest extends TestCase
{
    /** @test */
    public function it_can_create_an_instance_through_error_string()
    {
        $exception = MailChimpClientException::fromClientString(
            '400: Please provide a valid email address.'
        );

        $this->assertInstanceOf(MailChimpClientException::class, $exception);

        $this->assertEquals(400, $exception->getCode());

        $this->assertEquals('Please provide a valid email address.', $exception->getMessage());
    }
}
