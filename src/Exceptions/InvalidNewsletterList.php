<?php

namespace Spatie\Newsletter\Exceptions;

use Exception;

class InvalidNewsletterList extends Exception
{
    public static function noListWithName(string $name): self
    {
        return new self("There is no list named `{$name}`.");
    }

    public static function noListWithId(string $id, string $name): self
    {
        return new self("There is no list with id `{$id}`: (used for name `{$name}`.)");
    }

    public static function defaultListDoesNotExist($defaultListName): self
    {
        return new self("Could not find a default list named `{$defaultListName}`.");
    }
}
