<?php

return [
    'name' => 'Laravel Autodeploy 2.0',

    'branch' => env('AUTODEPLOY', 'production'),

    'deploy_de' => env('DEPLOY_DE', 'master'),

    'deploy_para' => env('DEPLOY_PARA', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Errors
    |--------------------------------------------------------------------------
    | Uma lista com possíveis erros que são retornados na linha de comando.
    | Caso algum texto seja encontrado ao executar os comandos, os próximos comandos serão cancelados.
    */

    'errors_log' => [
        'CONFLICT',
        'error: ',
        'fatal'
    ],

    'commands' => [
        /*
        |--------------------------------------------------------------------------
        | Comandos local
        |--------------------------------------------------------------------------
        | Coloque aqui os comandos que serão executados na sua máquina
        */
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

        /*
        |--------------------------------------------------------------------------
        | Comandos do servidor
        |--------------------------------------------------------------------------
        | Coloque aqui os comandos que serão executados no servidor
        */
        'servidor' => [
            'git fetch --all',
            'git reset --hard origin/{branch}'
            // executing composer from php script:
            //`echo '1' | php composer install --no-interaction`
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Folder Git
    |--------------------------------------------------------------------------
    | Não usar helpers como: base_path(),
    | Caminho para a pasta .git
    | no servidor, o comando inicia na pasta public, www ou public_html
    | "../" subir a partir da pasta public/www/public_html
    */
    'folder_git' => '../',

    /*
    |--------------------------------------------------------------------------
    | Folder Name Git
    |--------------------------------------------------------------------------
    | O nome da pasta git. Padrão .git,
    */
    'folder_name_git' => '.git',

    /*
    |--------------------------------------------------------------------------
    | Notificações no Desktop
    |--------------------------------------------------------------------------
    | Um aviso na tela aparece depois que for executados todos os comandos
    */

    'desktop_notification' => true,

    /*
    |--------------------------------------------------------------------------
    | Request Body
    |--------------------------------------------------------------------------
    | Salvar todo o corpo enviado por POST do repositório?
    */
    'save_request_body' => true
];
