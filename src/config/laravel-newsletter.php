<?php

return [

    'mailChimp' => [
        /*
         * The api key of your MailChimp account
         */
        'apiKey' => getenv('MAILCHIMP_APIKEY'),

        /*
         * Here you can define properties of the lists you want to
         * send campaigns.
         */
        'lists' => [
            /*
             * This key is used to identify this list. It can be used
             * in the various methods provided by this package.
             *
             * You can set it to any string you want and you can add
             * as many lists as you want.
             */
            'subscribers' => [
                /*
                 * The MailChimp id of this list
                 */
                'id' => '',
                'fromEmail' => '',
                'fromName' => '',
                'toName' => '',
                'default' => true,
            ],
        ],
    ],
];
