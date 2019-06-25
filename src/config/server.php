<?php

return [
    'name' => 'LaravelAutodeploy',
    'branch' => env('AUTODEPLOY', 'production'),
    'deploy_de' => env('DEPLOY_DE', 'master'),
    'deploy_para' => env('DEPLOY_PARA', 'production'),
    'errors_log' => [
        'CONFLICT',
        'error: ',
        'fatal'
    ],
    'commands' => [
        'servidor' => [
            'git fetch --all',
            'git reset --hard origin/{branch}'
        ]
    ],
    'folder_git' => __DIR__ . '/../../../../../',
    'desktop_notification' => true
];