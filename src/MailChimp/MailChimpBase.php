<?php

namespace Spatie\Newsletter\MailChimp;

use Illuminate\Contracts\Config\Repository;
use Mailchimp;
use Config;
use Exception;

abstract class MailChimpBase
{
    /**
     * @var mailChimp
     */
    protected $mailChimp;
    /**
     * @var Repository
     */
    private $config;

    public function __construct(Repository $config, MailChimpApi $mailChimp)
    {
        $this->mailChimp = $mailChimp;
        $this->config = $config;
    }

    /**
     * Convert a string to a MailChimp list id.
     *
     * @param string $listName
     * @return integer mixed
     * @throws Exception
     */
    protected function getListProperties($listName)
    {
        $properties = $this->config->get('newsletter.mailChimp.lists.'.$listName);
        if (! count($properties)) {
            throw new Exception('Unknown list name for mailChimp: '.$listName);
        }

        return $properties;
    }
}
