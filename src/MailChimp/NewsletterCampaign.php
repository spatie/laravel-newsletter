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
                'from_email' => $this->getCreateCampaignProperty($listProperties, 'fromEmail'),
                'from_name' => $this->getCreateCampaignProperty($listProperties, 'fromName'),
                'to_name' => $this->getCreateCampaignProperty($listProperties, 'toName'),
            ],
            [
                'html' => $content,
            ]);
    }

    /**
     * Method to provide backwards compatibility with older versions of the config file.
     *
     * @param $listProperties
     * @param $property
     * @return string
     */
    private function getCreateCampaignProperty($listProperties, $property)
    {
        return (isset($listProperties['createCampaign']) ? $listProperties['createCampaign'][$property] : $listProperties[$property]);
    }
}
