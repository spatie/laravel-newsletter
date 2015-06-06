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
                $this->value($listProperties['subscribe']['emailType'], 'html'),  //e-mail type
                $this->value($listProperties['subscribe']['requireDoubleOptin'], false),   //require double optin
                $this->value($listProperties['subscribe']['updateExistingUser'], false)    //update existing user
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
            $this->value($listProperties['unsubscribe']['deletePermanently'], false),  //delete permanently
            $this->value($listProperties['unsubscribe']['sendGoodbyeEmail'], false),  //send goodbye mail?
            $this->value($listProperties['unsubscribe']['sendUnsubscribeEmail'], false)   //send unsubscribe mail?
        );
    }

    /**
     * Get the value of the given $value. If it is not set, return the $default
     * Only exists to ensure backwards compatibility.
     *
     * @param $value
     * @param $default
     *
     * @return string
     */
    public function value($value, $default)
    {
        return (isset($value)) ? $value : $default;
    }


}
