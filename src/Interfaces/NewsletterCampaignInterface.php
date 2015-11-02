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

    /**
     * Create a new newsletter campaign.
     *
     * @param $cid string
     * @param $name string The parameter name ( see campaigns/create() ). This will be that parameter name (options, content, segment_opts) except "type_opts"
     * @param $value array An appropriate set of values for the parameter ( see campaigns/create() ). For additional parameters, this is the same value passed to them.
     *
     * @return mixed
     */
    public function update($cid, $name, $value);

    /**
     * Send a test newsletter campaign.
     *
     * @param $list
     * @param $subject
     * @param $content
     *
     * @return mixed
     */
    public function sendTest($cid, $emails, $send_type);

    /**
     * Send a newsletter campaign.
     *
     * @param $list
     * @param $subject
     * @param $content
     *
     * @return mixed
     */
    public function send($cid);

    /**
     * Delete a new newsletter campaign.
     *
     * @param $cid
     *
     * @return mixed
     */
    public function delete($cid);
}
