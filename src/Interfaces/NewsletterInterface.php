<?php namespace Spatie\Newsletter\Interfaces;

/**
 * Interface NewsletterCampaign.
 */
interface NewsletterInterface
{
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
     * Subscribe the email address to given list.
     *
     * @param $email
     * @param $list
     *
     * @return mixed
     */
    public function subscribe($email, $list = '');

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
