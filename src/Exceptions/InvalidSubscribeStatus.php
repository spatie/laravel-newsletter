<?php

namespace Spatie\Newsletter\Exceptions;

use Exception;

class InvalidSubscribeStatus extends Exception
{
    /**
     * @return static
     */
    public static function invalidStatus($defaultSubscribeStatus)
    {
        return new static('`{$defaultSubscribeStatus}` is not a valid subscribe status value');
    }
}
