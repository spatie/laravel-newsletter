<?php


return [

    'mailChimp' => [
            'apiKey' => getenv('MAILCHIMP_APIKEY'),

            /*
             * Here you can define properties of the lists you want to
             * send campaigns
             */
            'lists' => [
                    /*
                     * This key is used in the various methods provided by this package.
                     * You can set it to any string you want
                     */
                    'defaultList' => [
                            'id' => 'e6e17ec687',
                            'fromEmail' => 'info@bodartservicehouse.be',
                            'fromName' => 'Bodart Service House',
                            'toName' => 'Klanten',
                        ],

                ],
        ],
];
