<?php

namespace Spatie\Newsletter\MailChimp;

use Illuminate\Contracts\Config\Repository;
use Exception;
use Illuminate\Support\Facades\App;
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

    public function __construct(App $app, Repository $config)
    {
        $this->mailChimp = app()['laravel-newsletter-mailchimp'];
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
        foreach ($this->getAllLists() as $listName => $listProperties) {
            if (isset($listProperties['default']) && $listProperties['default']) {
                return $listName;
            }
        }

        throw new Exception('There is no default mailchimp list configured');
    }
}
