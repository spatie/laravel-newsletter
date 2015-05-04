<?php namespace Spatie\Newsletter;

/**
 * Interface NewsletterCampaign.
 */
interface NewsletterCampaign
{
    /**
     * Create a new newsletter campaign.
     *
     * @param $list
     * @param $subject
     * @param $content
     * @return mixed
     */
    public function create($list, $subject, $content);
}
