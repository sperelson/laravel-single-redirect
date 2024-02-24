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
];
