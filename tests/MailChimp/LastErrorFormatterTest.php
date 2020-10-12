<?php

namespace Spatie\Newsletter\Test;

use Illuminate\Support\Facades\Log;
use Mockery;
use Spatie\Newsletter\Newsletter;
use Spatie\Newsletter\NewsletterListCollection;
use Spatie\Newsletter\NullDriver;
use DrewM\MailChimp\MailChimp;

class LastErrorFormatterTest extends \PHPUnit\Framework\TestCase
{
    /** @var Mockery\Mock */
    protected $mailChimpApi;

    /** @var \Spatie\Newsletter\Newsletter */
    protected $newsletter;

    public function setUp(): void
    {
        $this->mailChimpApi = Mockery::mock(MailChimp::class);

        $newsletterLists = NewsletterListCollection::createFromConfig(
            [
                'lists' => [
                    'list1' => ['id' => 123],
                    'list2' => ['id' => 456],
                ],
                'defaultListName' => 'list1',
            ]
        );

        $this->newsletter = new Newsletter($this->mailChimpApi, $newsletterLists);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if ($container = Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        Mockery::close();
    }

    /** @test */
    public function it_can_format_last_error()
    {
        $this->mailChimpApi->shouldReceive('getLastErrorFormatted');
    }
}
