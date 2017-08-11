<?php

namespace Spatie\Newsletter;

class NewsletterList
{
    /** @var string */
    public $name;

    /** @var array */
    public $properties = [];

    public function __construct(string $name, array $properties)
    {
        $this->name = $name;
        $this->properties = $properties;
    }

    public function getId(): string
    {
        return $this->properties['id'];
    }

    public function getName(): string
    {
        return $this->name;
    }
}
