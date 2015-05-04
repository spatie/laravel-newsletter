<?php namespace Spatie\Newsletter;

interface NewsletterList
{
    /**
     * Subscribe the email address to given list
     *
     * @param $list
     * @param $email
     *
     * @return mixed
     */
    public function subscribeTo($list, $email);

    /**
     * Unsubscribe the email address to given list
     *
     * @param $list
     * @param $email
     *
     * @return mixed
     */
    public function unsubscribeFrom($list, $email);
}
