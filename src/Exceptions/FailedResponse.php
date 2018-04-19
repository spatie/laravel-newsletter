<?php

namespace Spatie\Newsletter\Exceptions;

use Exception;

class FailedResponse extends Exception
{
    public $response;
    
    public function __construct($response)
    {
        $this->response = $response;
    }
}
