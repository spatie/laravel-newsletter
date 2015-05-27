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

        try {
            return $this->mailChimp->lists->subscribe(
                $listProperties['id'],
                compact('email'),
                null,    //merge vars
                $listProperties['subscribe']['emailType'],  //e-mail type
                $listProperties['subscribe']['requireDoubleOptin'],   //require double optin
                $listProperties['subscribe']['updateExistingUser']    //update existing user
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

        return $this->mailChimp->lists->unsubscribe(
            $listProperties['id'],
            compact('email'),
            $listProperties['unsubscribe']['deletePermanently'],  //delete permanently
            $listProperties['unsubscribe']['sendGoodbyeEmail'],  //send goodbye mail?
            $listProperties['unsubscribe']['sendUnsubscribeEmail']   //send unsubscribe mail?
        );
    }
}
