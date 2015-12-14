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
        return $this->campaign
            ->create($subject, $content, $list);
    }

    /**
     * Update a newsletter campaign.
     *
     * @param $campaignId string
     * @param $name string
     * @param $value array
     *
     * @return mixed
     */
    public function updateCampaign($campaignId, $name, $value = [])
    {
        return $this->campaign
            ->update($campaignId, $name, $value);
    }


    /**
     * Delete a newsletter campaign.
     *
     * @param $campaignId
     *
     * @return mixed
     */
    public function deleteCampaign($campaignId)
    {
        return $this->campaign
            ->delete($campaignId);
    }


    /**
     * Send a test newsletter campaign.
     *
     * @param $campaignId string
     * @param $emails array
     * @param $sendType string
     *
     * @return mixed
     */
    public function sendTestCampaign($campaignId, $emails = [], $sendType = '')
    {
        return $this->campaign
            ->sendTest($campaignId, $emails, $sendType);
    }

    /**
     * Send a newsletter campaign.
     *
     * @param $campaignId string
     *
     * @return mixed
     */
    public function sendCampaign($campaignId)
    {
        return $this->campaign
            ->send($campaignId);
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
        return $this->list
            ->subscribe($email, $mergeVars, $list);
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
        return $this->list
            ->updateMember($email, $mergeVars, $list);
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
        return $this->list
            ->unsubscribe($email, $list);
    }


    /**
     * Get the instance of the underlying api
     *
     * @return mixed
     */
    public function getApi()
    {
        return $this->list
            ->getApi();
    }
}
