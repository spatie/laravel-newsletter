<?php

namespace Spatie\Newsletter\Support;

use Illuminate\Support\Collection;
use Spatie\Newsletter\Exceptions\InvalidNewsletterList;

class Lists extends Collection
{
    public string $defaultListName = '';

    public static function createFromConfig(array $config): self
    {
        $collection = new self();

        foreach ($config['lists'] as $name => $listProperties) {
            $collection->push(new NewsletterList($name, $listProperties));
        }

        $collection->defaultListName = $config['default_list_name'];

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
