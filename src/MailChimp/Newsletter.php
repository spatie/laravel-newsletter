<?php namespace Spatie\Newsletter\MailChimp;

use Spatie\Newsletter\Interfaces\Newsletter as NewsletterInterface;
use Spatie\Newsletter\Interfaces\NewsletterCampaign;
use Spatie\Newsletter\Interfaces\NewsletterList;

class Newsletter implements NewsletterInterface
{
    /**
     * @var NewsletterCampaign
     */
    private $campaign;
    /**
     * @var NewsletterList
     */
    private $list;

    public function __construct(NewsletterCampaign $campaign, NewsletterList $list)
    {
        $this->campaign = $campaign;
        $this->list = $list;
    }

    /**
     * Create a new newsletter campaign.
     *
     * @param $list
     * @param $subject
     * @param $content
     *
     * @return mixed
     */
    public function createCampaign($list, $subject, $content)
    {
        return $this->campaign->create($list, $subject, $content);
    }

    /**
     * Subscribe the email address to given list.
     *
     * @param $email
     * @param $list
     *
     * @return mixed
     */
    public function subscribe($email, $list = '')
    {
        return $this->list->subscribe($email, $list);
    }

    /**
     * Unsubscribe the email address to given list.
     *
     * @param $email
     * @param $list
     *
     * @return mixed
     */
    public function unsubscribe($email, $list = '')
    {
        return $this->list->unsubscribe($email, $list);
    }
}
