<?php

return [
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
    'folder_git' => '../', //ir para acima da pasta public
    'desktop_notification' => true
];