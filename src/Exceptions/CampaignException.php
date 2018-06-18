<?php

namespace Spatie\Newsletter\Exceptions;

use Exception;

class CampaignException extends Exception
{
    /**
     * @return static
     */
    public static function withMessage($message)
    {
        return new static($message);
    }


}
