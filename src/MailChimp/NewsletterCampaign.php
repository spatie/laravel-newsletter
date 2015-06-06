<?php namespace Spatie\Newsletter\MailChimp;

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
    public function create($subject, $content, $listName = '')
    {
        $listProperties = $this->getListProperties($listName);

        return $this->mailChimp->campaigns->create('regular',
            [
                'list_id' => $listProperties['id'],
                'subject' => $subject,
                'from_email' => $this->getCreateCampaignProperty('fromEmail'),
                'from_name' => $this->getCreateCampaignProperty('fromName'),
                'to_name' => $this->getCreateCampaignProperty('toName'),
            ],
            [
                'html' => $content,
            ]);
    }

    /**
     * Method to provide backwards compatibility with older versions of the config file.
     *
     * @param $property
     * @return string
     */
    private function getCreateCampaignProperty($property)
    {
        return (isset($listProperties['createCampaign']) ? $listProperties['createCampaign'][$property] : $listProperties[$property]);
    }
}
