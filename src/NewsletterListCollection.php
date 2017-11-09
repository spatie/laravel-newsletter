<?php

namespace Spatie\Newsletter;

use Illuminate\Support\Collection;
use Spatie\Newsletter\Exceptions\InvalidNewsletterList;

class NewsletterListCollection extends Collection
{
    /** @var string */
    public $defaultListName = '';

    public static function createFromConfig(array $config): self
    {
        $collection = new static();

        foreach ($config['lists'] as $name => $listProperties) {
            $collection->push(new NewsletterList($name, $listProperties));
        }

        $collection->defaultListName = $config['defaultListName'];

        return $collection;
    }

    public function findByName(string $name): NewsletterList
    {
        if ($name === '') {
            return $this->getDefault();
        }

        foreach ($this->items as $newsletterList) {
            if ($newsletterList->getName() === $name) {
                return $newsletterList;
            }
        }

        throw InvalidNewsletterList::noListWithName($name);
    }

    public function getDefault(): NewsletterList
    {
        foreach ($this->items as $newsletterList) {
            if ($newsletterList->getName() === $this->defaultListName) {
                return $newsletterList;
            }
        }

        throw InvalidNewsletterList::defaultListDoesNotExist($this->defaultListName);
    }
}
