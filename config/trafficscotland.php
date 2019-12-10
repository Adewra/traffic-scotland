<?php

return [

    /**
     * Just flip the switch to enable the functionality
     *
     *  Not yet implemented.
     *
     */
    'functionality' => [
        //'news' => true,

        'incidents' => true,

        'roadworks' => [
            'current' => true,
            'planned' => true,
        ],

        /*'traffic_status' => true,

        'live_traffic_cameras' => true,

        'park_and_ride' => true,

        'bridge_wind_restrictions_forecast' => true,

        'police_travel_warnings' => true,

        'variable_message_signs' => true,*/

        'events' => true,

        /*'gritters' => true*/
    ],

    'collection_methods' => [
        'rss_feeds' => true,
        'api' => false,
        'webpage_scraping' => false,
    ],

    'storage' => true,

    /**
     * Limit collection to one or more regions, or all regions by using '*'
     *                                    ^ (Spelling matters, pick from the list below)
     * HighlandAndWesternIsles, Grampian, CentralTaysideAndFife, Strathclyde, SouthWestScotlandLothianAndBorders
     *
     *  Not yet implemented.
     *
     */
    'regions' => [
        '*'
    ],
];