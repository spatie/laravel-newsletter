<?php namespace Spatie\Newsletter\Interfaces;

/**
 * Interface NewsletterCampaign.
 */
interface NewsletterCampaignInterface
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
    public function create($list, $subject, $content);
}
