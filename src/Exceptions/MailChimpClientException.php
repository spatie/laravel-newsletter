<?php

namespace Spatie\Newsletter\Exceptions;

use Exception;

class MailChimpClientException extends Exception
{

    /**
     * @param string $error
     * @return static
     */
    public static function fromClientString(string $error): self
    {
        $errorData = explode(': ', $error);

        return new static($errorData[1], $errorData[0]);
    }
}
