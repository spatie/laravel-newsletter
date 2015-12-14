<?php namespace Spatie\Newsletter\Interfaces;

interface NewsletterListInterface
{
    /**
     * Subscribe the email address to given list.
     *
     * @param $email
     * @param array $mergeVars
     * @param string $list
     * @return mixed
     */
    public function subscribe($email, $mergeVars = [], $list = '');

    /**
     * Unsubscribe the email address to given list.
     *
     * @param $email
     * @param $list
     *
     * @return mixed
     */
    public function unsubscribe($email, $list = '');

    /**
     * Update a member subscibed to a list
     *
     * @param $email
     * @param $list
     *
     * @return mixed
     */
    public function updateMember($email, $mergeVars, $listName);
}
