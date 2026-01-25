<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*'],               // allow all api routes
    'allowed_methods' => ['*'],         // GET, POST, PUT, DELETE, etc.
    'allowed_origins' => ['http://localhost:5173'], // your Vue dev server
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,    

];
