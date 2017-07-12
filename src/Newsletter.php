<?php

namespace Spatie\Newsletter;

use DrewM\MailChimp\MailChimp;

class Newsletter
{
    /** @var \DrewM\MailChimp\MailChimp */
    protected $mailChimp;

    /** * @var \Spatie\Newsletter\NewsletterListCollection */
    protected $lists;

    /**
     * @param \DrewM\MailChimp\MailChimp                  $mailChimp
     * @param \Spatie\Newsletter\NewsletterListCollection $lists
     */
    public function __construct(MailChimp $mailChimp, NewsletterListCollection $lists)
    {
        $this->mailChimp = $mailChimp;

        $this->lists = $lists;
    }

    /**
     * @param string $email
     * @param array  $mergeFields
     * @param string $listName
     * @param array  $options
     *
     * @return array|bool
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function subscribe($email, $mergeFields = [], $listName = '', $options = [])
    {
        $list = $this->lists->findByName($listName);

        $options = $this->getSubscriptionOptions($email, $mergeFields, $options);

        $response = $this->mailChimp->post("lists/{$list->getId()}/members", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    /**
     * @param string $email
     * @param array  $mergeFields
     * @param string $listName
     * @param array  $options
     *
     * @return array|bool
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function subscribeOrUpdate($email, $mergeFields = [], $listName = '', $options = [])
    {
        $list = $this->lists->findByName($listName);

        $options = $this->getSubscriptionOptions($email, $mergeFields, $options);

        $response = $this->mailChimp->put("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    /**
     * @param string $listName
     *
     * @param array $parameters
     * @return array|bool
     */
    public function getMembers($listName = '', $parameters = [])
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members", $parameters);
    }

    /**
     * @param string $email
     * @param string $listName
     *
     * @return array|bool
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function getMember($email, $listName = '')
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}");
    }

    /**
     * @param string $email
     * @param string $listName
     *
     * @return array|bool
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function getMemberActivity($email, $listName = '')
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}/activity");
    }

    /**
     * @param string $email
     * @param string $listName
     *
     * @return bool
     */
    public function hasMember($email, $listName = '')
    {
        $response = $this->getMember($email, $listName);

        if (! isset($response['email_address'])) {
            return false;
        }

        if (strtolower($response['email_address']) != strtolower($email)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $email
     * @param string $listName
     *
     * @return bool
     */
    public function isSubscribed($email, $listName = '')
    {
        $response = $this->getMember($email, $listName);

        if (! isset($response)) {
            return false;
        }

        if ($response['status'] != 'subscribed') {
            return false;
        }

        return true;
    }

    /**
     * @param $email
     * @param string $listName
     *
     * @return array|false
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function unsubscribe($email, $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->patch("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}", [
            'status' => 'unsubscribed',
        ]);

        return $response;
    }

    /**
     * Update the email address of an existing list member.
     *
     * @param string $currentEmailAddress
     * @param string $newEmailAddress
     * @param string $listName
     *
     * @return array|false
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function updateEmailAddress($currentEmailAddress, $newEmailAddress, $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->patch("lists/{$list->getId()}/members/{$this->getSubscriberHash($currentEmailAddress)}", [
            'email_address' => $newEmailAddress,
        ]);

        return $response;
    }

    /**
     * @param $email
     * @param string $listName
     *
     * @return array|false
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function delete($email, $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->delete("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}");

        return $response;
    }

    /**
     * @param string $fromName
     * @param string $replyTo
     * @param string $subject
     * @param string $html
     * @param string $listName
     * @param array  $options
     * @param array  $contentOptions
     *
     * @return array|bool
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function createCampaign($fromName, $replyTo, $subject, $html = '', $listName = '', $options = [], $contentOptions = [])
    {
        $list = $this->lists->findByName($listName);

        $defaultOptions = [
            'type' => 'regular',
            'recipients' => [
                'list_id' => $list->getId(),
            ],
            'settings' => [
                'subject_line' => $subject,
                'from_name' => $fromName,
                'reply_to' => $replyTo,
            ],
        ];

        $options = array_merge($defaultOptions, $options);

        $response = $this->mailChimp->post('campaigns', $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        if ($html === '') {
            return $response;
        }

        if (! $this->updateContent($response['id'], $html, $contentOptions)) {
            return false;
        }

        return $response;
    }

    /**
     * @param $campaignId
     * @param $html
     * @param array $options
     * @return array|bool|false
     */
    public function updateContent($campaignId, $html, $options = [])
    {
        $defaultOptions = compact('html');

        $options = array_merge($defaultOptions, $options);

        $response = $this->mailChimp->put("campaigns/{$campaignId}/content", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    /**
     * @return \DrewM\MailChimp\MailChimp
     */
    public function getApi()
    {
        return $this->mailChimp;
    }

    /**
     * @return array|false
     */
    public function getLastError()
    {
        return $this->mailChimp->getLastError();
    }

    /**
     * @return bool
     */
    public function lastActionSucceeded()
    {
        return $this->mailChimp->success();
    }

    /**
     * @param string $email
     *
     * @return string
     */
    protected function getSubscriberHash($email)
    {
        return $this->mailChimp->subscriberHash($email);
    }

    /**
     * @param $email
     * @param $mergeFields
     * @param $options
     * @return array
     */
    protected function getSubscriptionOptions($email, $mergeFields, $options)
    {
        $defaultOptions = [
            'email_address' => $email,
            'status' => 'subscribed',
            'email_type' => 'html',
        ];

        if (count($mergeFields)) {
            $defaultOptions['merge_fields'] = $mergeFields;
        }

        $options = array_merge($defaultOptions, $options);

        return $options;
    }
}
