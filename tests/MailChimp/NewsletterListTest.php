<?php

namespace Spatie\Newsletter\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Newsletter\Support\NewsletterList;

class NewsletterListTest extends TestCase
{
    protected $newsletterList;

    public function setUp(): void
    {
        parent::setUp();

        $this->newsletterList = new NewsletterList('subscriber', [
            'id' => 'abc123',
            'marketing_permissions' => [
                'email' => 'abc123',
            ],
        ]);
    }

    /** @test */
    public function it_can_determine_the_name_of_the_list()
    {
        $this->assertSame('subscriber', $this->newsletterList->getName());
    }

    /** @test */
    public function it_can_determine_the_id_of_the_list()
    {
        $this->assertSame('abc123', $this->newsletterList->getId());
    }

    /** @test */
    public function it_can_get_a_marketing_permission_of_the_list()
    {
        $this->assertSame('abc123', $this->newsletterList->getMarketingPermission('email'));
    }
}
