<?php namespace Spatie\Newsletter\Interfaces;

/**
 * Interface NewsletterCampaign.
 */
interface NewsletterInterface
{
    /**
     * Delete a newsletter campaign.
     *
     * @param $cid
     *
     * @return mixed
     */
    public function deleteCampaign($cid);

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
     * @param $cid string
     * @param $name string
     * @param $value array
     *
     * @return mixed
     */
    public function updateCampaign($cid, $name, $value = []);

    /**
     * Send a test newsletter campaign.
     *
     * @param $subject
     * @param $content
     * @param $list
     *
     * @return mixed
     */
    public function sendTestCampaign($cid, $emails = [], $send_type = '');

    /**
     * Send a newsletter campaign.
     *
     * @param $subject
     * @param $content
     * @param $list
     *
     * @return mixed
     */
    public function sendCampaign($cid);

    /**
     * Subscribe the email address to given list.
     *
     * @param $email
     * @param array $mergeVars
     * @param string $list
     * @return mixed
     */
    public function subscribe($email, $mergeVars = [], $list = '');

    /**
     * Update a member subscibed to a list
     *
     * @param $email
     * @param array $mergeVars
     * @param string $list
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
     * Get the instance of the underlying api
     *
     * @return mixed
     */
    public function getApi();
}
