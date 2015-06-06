<?php

namespace Spatie\Newsletter\MailChimp;

use Spatie\Newsletter\Exceptions\AlreadySubscribed;
use Spatie\Newsletter\Exceptions\ServiceRefusedSubscription;
use Spatie\Newsletter\Interfaces\NewsletterListInterface;

class NewsletterList extends MailChimpBase implements NewsletterListInterface
{
    /**
     * Subscribe a user to a MailChimp list.
     *
     * @param $email
     * @param $listName
     *
     * @return array
     *
     * @throws ServiceRefusedSubscription
     * @throws AlreadySubscribed
     */
    public function subscribe($email, $listName = '')
    {
        $listProperties = $this->getListProperties($listName);

        $emailType = 'html';
        $requireDoubleOptin = false;
        $updateExistingUser = false;

        if (isset($listProperties['subscribe'])) {
            $emailType = $listProperties['subscribe']['emailType'];
            $requireDoubleOptin = $listProperties['subscribe']['requireDoubleOptin'];
            $updateExistingUser = $listProperties['subscribe']['updateExistingUser'];
        }

        try {
            return $this->mailChimp->lists->subscribe(
                $listProperties['id'],
                compact('email'),
                null,    //merge vars
                $emailType,
                $requireDoubleOptin,
                $updateExistingUser
            );
        } catch (\Mailchimp_List_AlreadySubscribed $exception) {
            throw new AlreadySubscribed();
        } catch (\Mailchimp_Error $exception) {
            throw new ServiceRefusedSubscription($exception->getMessage());
        }
    }

    /**
     * Unsubscribe a user from a MailChimp list.
     *
     * @param $email
     * @param $listName
     *
     * @return mixed
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

}
