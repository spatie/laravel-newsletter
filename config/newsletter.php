<?php

return [

    /*
     * The driver to use to interact with MailChimp API.
     * You may use "log" or "null" to prevent calling the
     * API directly from your environment.
     */
    'driver' => env('NEWSLETTER_DRIVER', Spatie\Newsletter\Drivers\MailcoachDriver::class),

    /**
     * These arguments will be given to the driver.
     */
    'driver_arguments' => [
        'api_key' => env('NEWSLETTER_API_KEY'),

        'endpoint' => env('NEWSLETTER_ENDPOINT'),
    ],

    /*
     * The list name to use when no list name is specified in a method.
     */
    'default_list_name' => 'subscribers',

    'lists' => [

        /*
         * This key is used to identify this list. It can be used
         * as the listName parameter provided in the various methods.
         *
         * You can set it to any string you want and you can add
         * as many lists as you want.
         */
        'subscribers' => [

            /*
             * When using the Mailcoach driver, this should be Email list UUID
             * which is displayed in the Mailcoach UI
             *
             * When using the MailChimp driver, this should be a MailChimp list id.
             * http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id.
             */
            'id' => env('NEWSLETTER_LIST_ID'),
        ],
    ],
];
