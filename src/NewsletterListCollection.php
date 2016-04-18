<?php

namespace Spatie\Newsletter;

use Illuminate\Support\Collection;
use Spatie\Newsletter\Exceptions\InvalidNewsletterList;

class NewsletterListCollection extends Collection
{
    /**
     * @param array $lists
     *
     * @return static
     */
    public static function createFromArray($lists)
    {
        $collection = new static;
        
        foreach($lists as $name => $listProperties) {
            $collection->push(new NewsletterList($name, $listProperties));
        }

        return $collection;
    }

    /**
     * @param string $name
     * @return \Spatie\Newsletter\NewsletterList
     * 
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function findByName($name)
    {
        //dd($this->items);

        if ($name === '') {
            return $this->getDefault();
        }

        $list = $this->first(function ($index, NewsletterList $newletterList) use ($name) {
            return $newletterList->getName() === $name;
        });
        
        if (is_null($list)) {
            throw InvalidNewsletterList::noListWithName($name);
        }
        
        return $list;
    }

    /**
     * @return \Spatie\Newsletter\NewsletterList
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function getDefault()
    {
        $allLists = $this->getAllLists();

        if (!count($this)) {
            throw InvalidNewsletterList::noListsDefined();
        }

        if (count($allLists) > 2) {
            throw InvalidNewsletterList::cannotDetermineDefault();
        }

        return $this->first();
    }
}