<?php

namespace Spatie\Newsletter\Interfaces;

/**
 * Interface NewsletterCampaign.
 */
interface NewsletterInterface
{
    /**
     * Delete a newsletter campaign.
     *
     * @param $campaignId
     *
     * @return mixed
     */
    public function deleteCampaign($campaignId);

    /**
     * Create a new newsletter campaign.
     *
     * @param $list
     * @param $subject
     * @param $content
     *
     * @return mixed
     */
    public function createCampaign($list, $subject, $content);

    /**
     * Create a new newsletter campaign.
     *
     * @param $campaignId string
     * @param $name string
     * @param $value array
     *
     * @return mixed
     */
    public function updateCampaign($campaignId, $name, $value = []);

    /**
     * Send a test newsletter campaign.
     *
     * @param string       $campaignId
     * @param string|array $emails
     * @param string       $sendType
     *
     * @return mixed
     */
    public function sendTestCampaign($campaignId, $emails, $sendType = '');

    /**
     * Send a newsletter campaign.
     *
     * @param string $campaignId
     *
     * @return mixed
     */
    public function sendCampaign($campaignId);

    /**
     * Subscribe the email address to given list.
     *
     * @param $email
     * @param array  $mergeVars
     * @param string $list
     *
     * @return mixed
     */
    public function subscribe($email, $mergeVars = [], $list = '');

    /**
     * Update a member subscribed to a list.
     *
     * @param $email
     * @param array  $mergeVars
     * @param string $list
     *
     * @return mixed
     */
    public function updateMember($email, $mergeVars = [],  $list = '');

    /**
     * Unsubscribe the email address to given list.
     *
     * @param $email
     * @param $list
     *
     * @return mixed
     */
    public function unsubscribe($email, $list = '');

    /**
     * Get the instance of the underlying api.
     *
     * @return mixed
     */
    public function getApi();
}
