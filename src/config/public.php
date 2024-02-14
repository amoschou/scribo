<?php

return [

    'paths' => [
        'md' => resource_path('vendor/scribo/md'),
        'pdf' => storage_path('vendor/scribo/pdf'),
    ],

    'default_middleware' => [
        // 'auth',
        // 'verified',
    ],

    'binders' => [
        'docs' => [
            'title' => 'Title',
            'description' => 'Description',
            'github_repo' => 'githubusername/alpha',
            'github_api_token' => env('GH_API_TOKEN'),
            'path' => 'docs',
            'groups' => [
                //
            ],
        ],
    ],

    'prefix' => 'library',

    'folder_info' => '_index.yaml',

    'menu' => [
        //
    ],

];
