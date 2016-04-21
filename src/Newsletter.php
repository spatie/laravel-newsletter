<?php

namespace Spatie\Newsletter;

use DrewM\MailChimp\MailChimp;

class Newsletter
{
    /**
     * @var \DrewM\MailChimp\MailChimp
     */
    protected $mailChimp;
    /**
     * @var \Spatie\Newsletter\NewsletterListCollection
     */
    protected $lists;

    public function __construct(MailChimp $mailChimp, NewsletterListCollection $lists)
    {
        $this->mailChimp = $mailChimp;

        $this->lists = $lists;
    }

    public function subscribe($email, $mergeFields = [], $listName = '', $options = [])
    {
        $list = $this->lists->findByName($listName);

        $defaultOptions = [
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => $mergeFields,
            'email_type' => 'html'
        ];
        
        $options = array_merge($defaultOptions, $options);

        $response = $this->mailChimp->post("list/{$list->getId()}/members", $options);

        return $response;
    }
    

    public function unsubscribe($email, $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $subscriberHash = $this->mailChimp->subscriberHash('davy@example.com');

        $response = $this->mailChimp->delete("lists/{$list->getId()}/members/{$subscriberHash}");

        return $response;
    }

    /**
     * @return \DrewM\MailChimp\MailChimp
     */
    public function getApi()
    {
        return $this->mailChimp;
    }
    
    public function lastActionSucceeded()
    {
        return is_null($this->mailChimp->getLastError());
    }


}

