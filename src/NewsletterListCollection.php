<?php

namespace Spatie\Newsletter;

use Illuminate\Support\Collection;
use Spatie\Newsletter\Exceptions\InvalidNewsletterList;

class NewsletterListCollection extends Collection
{
    /** @var string */
    public $defaultListName = '';

    /**
     * @param array $config
     *
     * @return static
     */
    public static function createFromConfig(array $config)
    {
        $collection = new static();

        foreach ($config['lists'] as $name => $listProperties) {
            $collection->push(new NewsletterList($name, $listProperties));
        }

        $collection->defaultListName = $config['defaultListName'];

        return $collection;
    }

    /**
     * @param string $name
     *
     * @return \Spatie\Newsletter\NewsletterList
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function findByName($name)
    {
        if ((string) $name === '') {
            return $this->getDefault();
        }

        foreach ($this->items as $newsletterList) {
            if ($newsletterList->getName() === $name) {
                return $newsletterList;
            }
        }

        throw InvalidNewsletterList::noListWithName($name);
    }

    /**
     * @return \Spatie\Newsletter\NewsletterList
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function getDefault()
    {
        foreach ($this->items as $newsletterList) {
            if ($newsletterList->getName() === $this->defaultListName) {
                return $newsletterList;
            }
        }

        throw InvalidNewsletterList::defaultListDoesNotExist($this->defaultListName);
    }
}
