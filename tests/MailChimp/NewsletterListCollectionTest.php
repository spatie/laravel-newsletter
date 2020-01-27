<?php

namespace Spatie\Newsletter\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Newsletter\Exceptions\InvalidNewsletterList;
use Spatie\Newsletter\NewsletterList;
use Spatie\Newsletter\NewsletterListCollection;

class NewsletterListCollectionTest extends TestCase
{
    protected $newsletterListCollection;

    public function setUp(): void
    {
        parent::setUp();

        $this->newsletterListCollection = NewsletterListCollection::createFromConfig(
            [
                'lists' => [
                    'list1' => ['id' => 1],
                    'list2' => ['id' => 2],
                    'list3' => ['id' => 3],
                ],
                'defaultListName' => 'list3',
            ]
        );
    }

    /** @test */
    public function it_can_find_a_list_by_its_name()
    {
        $list = $this->newsletterListCollection->findByName('list2');

        $this->assertInstanceOf(NewsletterList::class, $list);

        $this->assertEquals(2, $list->getId());
    }

    /** @test */
    public function it_will_use_the_default_list_when_not_specifing_a_listname()
    {
        $list = $this->newsletterListCollection->findByName('');

        $this->assertInstanceOf(NewsletterList::class, $list);

        $this->assertEquals(3, $list->getId());
    }

    /** @test */
    public function it_will_throw_an_exception_when_using_a_default_list_that_does_not_exist()
    {
        $newsletterListCollection = NewsletterListCollection::createFromConfig(
            [
                'lists' => [
                    'list1' => ['id' => 'list1'],
                ],

                'defaultListName' => 'list2',
            ]
        );

        $this->expectException(InvalidNewsletterList::class);

        $newsletterListCollection->findByName('');
    }

    /** @test */
    public function it_will_throw_an_exception_when_trying_to_find_a_list_that_does_not_exist()
    {
        $this->expectException(InvalidNewsletterList::class);

        $this->newsletterListCollection->findByName('blabla');
    }
}
