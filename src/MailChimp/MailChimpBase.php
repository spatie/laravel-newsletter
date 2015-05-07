<?php

namespace Spatie\Newsletter\MailChimp;

use Illuminate\Contracts\Config\Repository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use MailChimp;

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

    public function __construct(Application $app, Repository $config)
    {
        $this->mailChimp = $app['laravel-newsletter-mailchimp'];
        $this->config = $config;
    }

    /**
     * Get all configured lists.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getAllLists()
    {
        $allLists =  $this->config->get('laravel-newsletter.mailChimp.lists');

        if (! count($allLists)) {
            throw new Exception('There are no MailChimp lists defined');
        }

        return $allLists;
    }

    /**
     * Get the instance of the underlying api
     *
     * @return MailChimp
     */
    public function getApi()
    {
        return $this->mailChimp;
    }

    /**
     * Convert all  properties for the given listName.
     *
     * @param string $listName
     *
     * @return array
     *
     * @throws Exception
     */
    protected function getListProperties($listName)
    {
        if ($listName == '') {
            $listName = $this->getDefaultListName();
        }

        foreach ($this->getAllLists() as $configuredListName => $listProperties) {
            if ($configuredListName == $listName) {
                return $listProperties;
            }
        }

        throw new Exception('Unknown list name for mailChimp: '.$listName);
    }

    /**
     * Get the name of the list that is marked as default.
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getDefaultListName()
    {
        $allLists = $this->getAllLists();

        if (! count($allLists))
        {
            throw new Exception('There are no lists defined');
        }

        if (count($allLists) > 2)
        {
            throw new Exception('You must specified a list name when you have multiple lists defined');
        }

        return key($this->getAllLists());
    }
}
