<?php namespace Spatie\Newsletter\MailChimp;

use Spatie\Newsletter\Interfaces\NewsletterCampaignInterface;

class NewsletterCampaign extends MailChimpBase implements NewsletterCampaignInterface
{
    /**
     * Create new MailChimp Campaign.
     *
     * @param $listName
     * @param $subject
     * @param $content
     *
     * @return \associative_array
     */
    public function create($listName, $subject, $content)
    {
        $listProperties = $this->getListProperties($listName);

        return $this->mailChimp->campaigns->create('regular',
            [
                'list_id' => $listProperties['id'],
                'subject' => $subject,
                'from_email' => $listProperties['fromEmail'],
                'from_name' => $listProperties['fromName'],
                'to_name' => $listProperties['toName'],

            ],
            [
                'html' => $content,
            ]);
    }
}
