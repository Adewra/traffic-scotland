<?php

return [

    /**
     * Enable or disable the capturing of extended accident details by visiting the permalink.
     */
    'scrape_data' => env('TRAFFICSCOTLAND_SCRAPE_DATA', false),

    /**
     * Also collect data from Highways England
     *
     *  Not yet implemented.
     *
     */
    'highways_england' => false,

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

    /**
     * Just flip the switch to enable the functionality
     *
     *  Not yet implemented.
     *
     */
    'news' => false,

    'current_incidents' => [
        'extended_details' => true,
        'weather_conditions' => true
    ],

    'roadworks' => [
        'current' => true,
        'planned' => true,
    ],

    'traffic_status' => false,

    'live_traffic_cameras' => false,

    'park_and_ride' => false,

    'bridge_wind_restrictions_forecast' => false,

    'police_travel_warnings' => false,

    'variable_message_signs' => false,

    'events' => true,

    'gritters' => false
];