<?php

namespace Spatie\Newsletter;

use Illuminate\Support\Facades\Log;

class NullDriver
{
    /**
     * @var bool
     */
    private $logCalls;

    public function __construct(bool $logCalls = false)
    {
        $this->logCalls = $logCalls;
    }

    public function __call($name, $arguments)
    {
        if ($this->logCalls) {
            Log::debug('Called Spatie Newsletter facade method: '.$name.' with:', $arguments);
        }
    }
}
