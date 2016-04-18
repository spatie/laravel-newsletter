<?php

namespace Spatie\Newsletter\Exceptions;

use Exception;

class InvalidNewsletterList extends Exception
{
    /**
     * @return static
     */
    public static function noListsDefined()
    {
        return new static("There are no lists defined");
    }

    /**
     * @return static
     */
    public static function cannotDetermineDefault()
    {
        return new static("When there is more than one list, you must explicitly choose which one to use");
    }

    /**
     * @param string $name
     * @return static
     */
    public static function noListWithName($name)
    {
        return new static("There is no list named `{$name}`");
    }
}
