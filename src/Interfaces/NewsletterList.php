<?php namespace Spatie\Newsletter\Interfaces;

interface NewsletterList
{
    /**
     * Subscribe the email address to given list.
     *
     * @param $email
     * @param $list
     *
     * @return mixed
     */
    public function subscribe($email, $list = '');

    /**
     * Unsubscribe the email address to given list.
     *
     * @param $email
     * @param $list
     *
     * @return mixed
     */
    public function unsubscribe($email, $list = '');
}
