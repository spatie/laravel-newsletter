<?php

namespace Spatie\Newsletter\Test;

use Spatie\Newsletter\Exceptions\InvalidNewsletterList;
use Spatie\Newsletter\NewsletterList;
use Spatie\Newsletter\NewsletterListCollection;

beforeEach(function () {
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
});

it('can find a list by its name', function () {
    $list = $this->newsletterListCollection->findByName('list2');

    $this->assertInstanceOf(NewsletterList::class, $list);

    $this->assertEquals(2, $list->getId());
});

it('will use the default list when not specifing a listname', function () {
    $list = $this->newsletterListCollection->findByName('');

    $this->assertInstanceOf(NewsletterList::class, $list);

    $this->assertEquals(3, $list->getId());
});

it('will throw an exception when using a default list that does not exist', function () {
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
});

it('will throw an exception when trying to find a list that does not exist', function () {
    $this->expectException(InvalidNewsletterList::class);

    $this->newsletterListCollection->findByName('blabla');
});
