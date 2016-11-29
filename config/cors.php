<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => false,
    'allowedOrigins' => ['*'],
    //'allowedHeaders' => ['*'],
    //'allowedMethods' => ['*'],
    'allowedHeaders' => [
        'Range', 
        'Origin', 
        'Content-Type', 
        'Accept', 
        'Authorization', 
        'X-Request-With', 
        'X-CSRF-Token', 
        'X-XSRF-Token'],
    'allowedMethods' => ['PATCH', 'POST', 'PUT', 'DELETE', 'GET', 'OPTIONS'],
    'exposedHeaders' => [],
    'maxAge' => 0,
    'hosts' => [],
];

