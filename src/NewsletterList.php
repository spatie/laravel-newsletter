<?php

namespace Spatie\Newsletter;

class NewsletterList
{
    /** @var string */
    public $name;

    /** @var array */
    public $properties = [];

    /**
     * @param string $name
     * @param array  $properties
     */
    public function __construct($name, array $properties)
    {
        $this->name = $name;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->properties['id'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
