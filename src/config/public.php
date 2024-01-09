<?php

return [

    'paths' => [
        'src' => resource_path('vendor/scribo/src'),
        'pdf' => storage_path('vendor/scribo/pdf'),
    ],

    'middleware' => [
        // 'auth',
        // 'verified',
    ],

    'binders' => [
        'default' => [
            'github_repo' => 'githubusername/alpha',
            'github_api_token' => env('GH_API_TOKEN'),
            'route_prefix' => 'docs',
        ],
    ],

];
