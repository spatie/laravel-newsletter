<?php

use Spatie\Newsletter\Exceptions\InvalidNewsletterList;
use Spatie\Newsletter\Support\Lists;
use Spatie\Newsletter\Support\NewsletterList;

beforeEach(function () {
    $this->lists = Lists::createFromConfig(
        [
            'lists' => [
                'list1' => ['id' => 1],
                'list2' => ['id' => 2],
                'list3' => ['id' => 3],
            ],
            'default_list_name' => 'list3',
        ]
    );
});

it('can find a list by its name', function () {
    $list = $this->lists->findByName('list2');

    $this->assertInstanceOf(NewsletterList::class, $list);

    $this->assertEquals(2, $list->getId());
});

it('can use the default list', function () {
    $list = $this->lists->findByName('');

    $this->assertInstanceOf(NewsletterList::class, $list);

    $this->assertEquals(3, $list->getId());
});

it('will throw an exception when the default list does not exist', function () {
    $lists = Lists::createFromConfig(
        [
            'lists' => [
                'list1' => ['id' => 'list1'],
            ],

            'default_list_name' => 'list2',
        ]
    );

    $lists->findByName('');
})->throws(InvalidNewsletterList::class);

it('will throw an exception when a list cannot be found', function () {
    $this->lists->findByName('blabla');
})->throws(InvalidNewsletterList::class);
