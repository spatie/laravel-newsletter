<?php

namespace Spatie\Newsletter\MailChimp;

use Spatie\Newsletter\Interfaces\NewsletterCampaignInterface;

class NewsletterCampaign extends MailChimpBase implements NewsletterCampaignInterface
{
    /**
     * Create new MailChimp Campaign.
     *
     *
     * @param $subject
     * @param $content
     * @param $listName
     *
     * @return \associative_array
     */
    public function create($subject, $content, $listName = '', $extraParameters)
    {
        $listProperties = $this->getListProperties($listName);

        return $this->mailChimp
            ->campaigns->create('regular',
                [
                    'list_id' => $listProperties['id'],
                    'subject' => $subject,
                    'from_email' => $this->getCreateCampaignProperty($listProperties, 'fromEmail'),
                    'from_name' => $this->getCreateCampaignProperty($listProperties, 'fromName'),
                    'to_name' => $this->getCreateCampaignProperty($listProperties, 'toName'),
                ],
                [
                    'html' => $content,
                ]);
    }

    /**
     * Create a new newsletter campaign.
     *
     * @param string $campaignId Campaign ID
     * @param string $fieldName  string The parameter name ( see campaigns/create() ). This will be that parameter name (options, content, segment_opts) except "type_opts"
     * @param $value array An appropriate set of values for the parameter ( see campaigns/create() ). For additional parameters, this is the same value passed to them.
     *
     * @return mixed
     */
    public function update($campaignId, $fieldName, $value)
    {
        return $this
            ->mailChimp
            ->campaigns
            ->update(
                $campaignId,
                $fieldName,
                $value
            );
    }

    /**
     * Send a test MailChimp Campaign.
     *
     * @param string       $campaignId
     * @param array|string $emails
     * @param string       $sendType
     *
     * @return mixed
     */
    public function sendTest($campaignId, $emails, $sendType = '')
    {
        if (!is_array($emails)) {
            $emails = [$emails];
        }

        return $this
            ->mailChimp
            ->campaigns
            ->sendTest($campaignId, $emails, $sendType);
    }

    /**
     * Send a MailChimp Campaign.
     *
     * @param string $campaignId
     *
     * @return mixed
     */
    public function send($campaignId)
    {
        return $this
            ->mailChimp
            ->campaigns
            ->send($campaignId);
    }

    /**
     * Delete a MailChimp Campaign.
     *
     *
     * @param $campaignId
     *
     * @return mixed
     */
    public function delete($campaignId)
    {
        return $this
            ->mailChimp
            ->campaigns
            ->delete($campaignId);
    }

    /**
     * Method to provide backwards compatibility with older versions of the config file.
     *
     * @param $listProperties
     * @param $property
     *
     * @return string
     */
    private function getCreateCampaignProperty($listProperties, $property)
    {
        return isset($listProperties['createCampaign'])
            ? $listProperties['createCampaign'][$property]
            : $listProperties[$property];
    }
}
