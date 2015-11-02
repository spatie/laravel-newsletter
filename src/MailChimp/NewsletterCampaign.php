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
                'text' => strip_tags($content),
            ]);
    }

    /**
     * Create a new newsletter campaign.
     *
     * @param $cid string Campaign ID
     * @param $name string The parameter name ( see campaigns/create() ). This will be that parameter name (options, content, segment_opts) except "type_opts"
     * @param $value array An appropriate set of values for the parameter ( see campaigns/create() ). For additional parameters, this is the same value passed to them.
     *
     * @return mixed
     */
    public function update($cid, $name, $value)
    {
        return $this->mailChimp->campaigns->update(
            $cid,
            $name,
            $value
        );
    }

    /**
     * Send a test MailChimp Campaign.
     *
     *
     * @param $cid
     *
     * @return mixed
     */
    public function sendTest($cid, $emails = [], $send_type = '')
    {
        return $this->mailChimp->campaigns->sendTest($cid, $emails, $send_type);
    }

    /**
     * Send a MailChimp Campaign.
     *
     *
     * @param $cid
     *
     * @return mixed
     */
    public function send($cid)
    {
        return $this->mailChimp->campaigns->send($cid);
    }

    /**
     * Delete a MailChimp Campaign.
     *
     *
     * @param $cid
     *
     * @return mixed
     */
    public function delete($cid)
    {
        return $this->mailChimp->campaigns->delete($cid);
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
