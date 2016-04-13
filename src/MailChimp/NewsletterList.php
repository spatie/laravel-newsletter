<?php

namespace Spatie\Newsletter\MailChimp;

use Spatie\Newsletter\Exceptions\AlreadySubscribed;
use Spatie\Newsletter\Exceptions\SubscriptionRefused;

class NewsletterList extends MailChimpBase
{
    /**
     * Subscribe a user to a MailChimp list.
     *
     * @param $email
     * @param array $mergeFields
     * @param string $listName
     *
     * @param $options
     * @return array
     */
    public function subscribe($email, $mergeFields = [], $listName = '', $options = [])
    {
        $listProperties = $this->getListProperties($listName);
        
        $defaultOptions = [[
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => $mergeFields,
            'email_type' => 'html'
        ]]
        
       $response = $this->mailChimp->post("list/{$listProperties['id']}/members", );
        
        $this->handleSubscriptionErrors();
        
        return $response;
    }

    /**
     * Unsubscribe a user from a MailChimp list.
     *
     * @param string $email
     * @param string $listName
     *
     * @return associative_array
     */
    public function unsubscribe($email, $listName = '')
    {
        $listProperties = $this->getListProperties($listName);

        $deletePermanently = false;
        $sendGoodbyeEmail = false;
        $sendUnsubscribeEmail = false;

        if (isset($listProperties['unsubscribe'])) {
            $deletePermanently = $listProperties['unsubscribe']['deletePermanently'];
            $sendGoodbyeEmail = $listProperties['unsubscribe']['sendGoodbyeEmail'];
            $sendUnsubscribeEmail = $listProperties['unsubscribe']['sendUnsubscribeEmail'];
        }

        return $this->mailChimp->lists->unsubscribe(
            $listProperties['id'],
            compact('email'),
            $deletePermanently,
            $sendGoodbyeEmail,
            $sendUnsubscribeEmail
        );
    }

    /**
     * Update a member subscribed to a list.
     *
     * @param string $email
     * @param array $mergeVars
     * @param bool $replaceInterests
     * @param string $listName
     *
     * @return \Spatie\Newsletter\MailChimp\associative_array
     *
     * @throws \Exception
     */
    public function updateMember($email, $mergeVars = [], $replaceInterests = true, $listName = '')
    {
        $listProperties = $this->getListProperties($listName);

        $emailType = 'html';

        if (isset($listProperties['updateMember'])) {
            $emailType = $listProperties['updateMember']['emailType'];
        }

        return $this
            ->mailChimp
            ->lists
            ->updateMember(
                $listProperties['id'],
                compact('email'),
                $mergeVars,
                $emailType,
                $replaceInterests
            );
    }

    private function handleSubscriptionErrors($response, $)
    {
        if ($this->mailChimp->getLastError() === false) {
            return;
        }
        
        throw new SubscriptionRefused($this->mailChimp->getLastError());
    }
}
