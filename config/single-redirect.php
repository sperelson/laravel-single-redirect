<?php

/*
|--------------------------------------------------------------------------
| Single Redirect Options
|--------------------------------------------------------------------------
*/
return [
    'enabled' => true,

    /*
     * Add extra headers to the internal requests
     * Example: ['test-runner' => 'abcdefgh']
     */
    'extra_headers' => [],

    /*
     * Apply only to the specified route groups
     * Example: ['web']
     */
    'groups' => [],

    /*
     * Number of times to test the redirects before failing with an exception
     */
    'redirect-count' => 10,

    /*
     * Will use HEAD by default
     * Set to true to use the request's method
     */
    'use-request-method' => false,
];
