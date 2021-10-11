<?php

return [
    /**
     * Domain of thesieure
     *
     */
    'domain' => 'thesieure.com',

    /**
     * Partner Id
     *
     */
    'id' => env('THESIEURE_ID', null),

    /**
     * Partner key (secrete)
     *
     */
    'key' => env('THESIEURE_KEY', null),

    /**
     * User login / created in seeder
     * When thesieure actings in app will use this user
     *
     */
    'user' => [
        'login' => 'thesieure',
    ],

    /**
     * Determine whether user can use this service for recharge cards
     *
     */
    'open_recharging_card' => true,
];
