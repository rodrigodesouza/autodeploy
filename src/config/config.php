<?php

return [
    'name' => 'Laravel Autodeploy',

    'branch' => env('AUTODEPLOY', 'production'),

    'deploy_de' => env('DEPLOY_DE', 'master'),

    'deploy_para' => env('DEPLOY_PARA', 'production'),

    'errors_log' => [
        'CONFLICT',
        'error: ',
        'fatal'
    ],

    'commands' => [
        'local' => [
            'git add . && git commit -m "{commit}"',
            'git pull origin {de}',
            'git push origin {de}',
            'git checkout {para}',
            'git merge {de}',
            'git add . && git commit -m "{commit}"',
            'git push origin {para}',
            'git checkout {de}'
        ],
        'servidor' => [
            'git fetch --all',
            'git reset --hard origin/{branch}'
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Folder Git
    |--------------------------------------------------------------------------
    | Não usar helpers como: base_path(),
    | "../" subir a partir da pasta public/www/public_html
    */
    'folder_git' => '../',

    'desktop_notification' => true,

    'save_request_body' => true
];