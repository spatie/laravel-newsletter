<?php

namespace Spatie\Newsletter\MailChimp;

use Spatie\Newsletter\Exceptions\AlreadySubscribed;
use Spatie\Newsletter\Exceptions\ServiceRefusedSubscription;
use Spatie\Newsletter\NewsletterList as NewsletterListInterface;

class NewsletterList extends MailChimpBase implements NewsletterListInterface
{
    /**
     * Subscribe a user to a MailChimp list.
     *
     * @param $listName
     * @param $email
     *
     * @return array
     *
     * @throws ServiceRefusedSubscription
     * @throws AlreadySubscribed
     */
    public function subscribeTo($listName, $email)
    {
        try {
            return $this->mailChimp->lists->subscribe(
                $this->getListProperties($listName)['id'],
                compact('email'),
                null,    //merge vars
                'html',  //e-mail type
                false,   //require double optin
                false    //update existing user
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
     * @param $listName
     * @param $email
     *
     * @return mixed
     */
    public function unsubscribeFrom($listName, $email)
    {
        return $this->mailChimp->lists->unsubscribe(
            $this->convertListNameToMailChimpListId($listName),
            compact('email'),
            false,  //delete permanently
            false,  //send goodbye mail?
            false   //send unsubscribe mail?
        );
    }
}
