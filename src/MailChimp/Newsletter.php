<?php namespace Spatie\Newsletter\MailChimp;

use Spatie\Newsletter\Interfaces\NewsletterInterface;
use Spatie\Newsletter\Interfaces\NewsletterCampaignInterface;
use Spatie\Newsletter\Interfaces\NewsletterListInterface;

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

    public function __construct(NewsletterCampaignInterface $campaign, NewsletterListInterface $list)
    {
        $this->campaign = $campaign;
        $this->list = $list;
    }


    /**
     * Create a new newsletter campaign.
     *
     * @param $subject
     * @param $content
     * @param $list
     *
     * @return mixed
     */
    public function createCampaign($subject, $content, $list = '')
    {
        return $this->campaign->create($subject, $content, $list);
    }

    /**
     * Update a newsletter campaign.
     *
     * @param $cid string
     * @param $name string
     * @param $value array
     *
     * @return mixed
     */
    public function updateCampaign($cid, $name, $value = [])
    {
        return $this->campaign->update($cid, $name, $value);
    }


    /**
     * Delete a newsletter campaign.
     *
     * @param $cid
     *
     * @return mixed
     */
    public function deleteCampaign($cid)
    {
        return $this->campaign->delete($cid);
    }


    /**
     * Send a test newsletter campaign.
     *
     * @param $cid string
     * @param $emails array
     * @param $send_type string
     *
     * @return mixed
     */
    public function sendTestCampaign($cid, $emails = [], $send_type = '')
    {
        return $this->campaign->sendTest($cid, $emails, $send_type);
    }

    /**
     * Send a newsletter campaign.
     *
     * @param $cid string
     *
     * @return mixed
     */
    public function sendCampaign($cid)
    {
        return $this->campaign->send($cid);
    }

    /**
     * Subscribe the email address to given list.
     *
     * @param $email
     * @param array $mergeVars
     * @param string $list
     * @return mixed
     */
    public function subscribe($email, $mergeVars = [],  $list = '')
    {
        return $this->list->subscribe($email, $mergeVars, $list);
    }

    /**
     * Update a member subscribed to a list
     *
     * @param $email
     * @param array $mergeVars
     * @param string $list
     *
     * @return mixed
     */
    public function updateMember($email, $mergeVars = [],  $list = '')
    {
        return $this->list->updateMember($email, $mergeVars, $list);
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


    /**
     * Get the instance of the underlying api
     *
     * @return mixed
     */
    public function getApi()
    {
        return $this->list->getApi();
    }
}
