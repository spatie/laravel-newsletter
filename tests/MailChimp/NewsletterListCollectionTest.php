<?php

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

    expect($list)->toBeInstanceOf(NewsletterList::class)
        ->and($list->getId())->toEqual(2);
});

it('will use the default list when not specifing a list name', function () {
    $list = $this->newsletterListCollection->findByName('');

    expect($list)->toBeInstanceOf(NewsletterList::class)
        ->and($list->getId())->toEqual(3);
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

    $newsletterListCollection->findByName('');
})->throws(InvalidNewsletterList::class);

it('will throw an exception when trying to find a list that does not exist', function () {
    $this->newsletterListCollection->findByName('blabla');
})->throws(InvalidNewsletterList::class);
