<?php

namespace Spatie\Newsletter\Exceptions;

use Exception;

class CouldNotUpdateMember extends Exception
{
    public static function make(string $email, Exception $exception): self
    {
        return new self("Could not update member {$email} because:".$exception->getMessage());
    }
}
