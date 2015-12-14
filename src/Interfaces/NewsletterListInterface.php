<?php

namespace Spatie\Newsletter\Interfaces;

interface NewsletterListInterface
{
    /**
     * Subscribe the email address to given list.
     *
     * @param string $email
     * @param array  $mergeVars
     * @param string $list
     *
     * @return mixed
     */
    public function subscribe($email, $mergeVars = [], $list = '');

    /**
     * Unsubscribe the email address to given list.
     *
     * @param string $email
     * @param $list
     *
     * @return mixed
     */
    public function unsubscribe($email, $list = '');

    /**
     * Update a member subscribed to a list.
     *
     * @param string $email
     * @param array  $mergeVars
     * @param bool   $replaceInterests
     * @param string $listName
     * 
     * @return mixed
     */
    public function updateMember($email, $mergeVars, $replaceInterests = true, $listName);
}
