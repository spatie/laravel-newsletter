<?php

namespace Spatie\Newsletter\Test;

use PHPUnit_Framework_TestCase;
use Spatie\Newsletter\Exceptions\InvalidNewsletterList;
use Spatie\Newsletter\NewsletterList;
use Spatie\Newsletter\NewsletterListCollection;

class NewsletterListCollectionTest extends PHPUnit_Framework_TestCase
{
    protected $newsletterListCollection;

    public function setUp()
    {
        parent::setUp();

        $this->newsletterListCollection = NewsletterListCollection::createFromArray(
            [
                'list1' => ['id' => 1],
                'list2' => ['id' => 2],
                'list3' => ['id' => 3],
            ]
        );
    }

    /** @test */
    public function it_can_find_a_list_by_its_name()
    {
        $list = $this->newsletterListCollection->findByName('list2');

        $this->assertInstanceOf(NewsletterList::class, $list);

        $this->assertSame(2, $list->getId());
    }

    /** @test */
    public function it_will_use_the_first_list_not_specifing_a_listname()
    {

        $this->newsletterListCollection = NewsletterListCollection::createFromArray(
            [
                'list1' => ['id' => 1]
            ]
        );

        $list = $this->newsletterListCollection->findByName('');

        $this->assertInstanceOf(NewsletterList::class, $list);

        $this->assertSame(1, $list->getId());
    }

    /** @test */
    public function it_will_throw_an_exception_when_trying_to_find_a_list_that_does_not_exist()
    {
        $this->setExpectedException(InvalidNewsletterList::class);
        
        $this->newsletterListCollection->findByName('blabla');
    }
}
