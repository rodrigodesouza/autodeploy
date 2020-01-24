<?php

namespace Rd7\Autodeploy\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Config\Repository;
use Rd7\Autodeploy\Git\GitRepository;
use Rd7\Autodeploy\Config\GetConfig;

class AutodeployController extends Controller
{
    public function webhook(Request $request)
    {
        try {
            $input = $request->all();

            $config = (new GetConfig)->getConfig();
            if ($request->isMethod('post')) {

                if ($config->get('save_request_body') == true){
                    $this->saveLog($input);
                }

                $branch = $config->get('branch');

                if (isset($input['ref']) and $input['ref'] == 'refs/heads/' . $branch) {

                    if(count($config->get('commands.servidor'))) {
                        foreach($config->get('commands.servidor') as $command) {

                            $command = str_replace("{branch}", $branch, $command);

                            $prefixo = "cd " . $config->get('folder_git');
                            $command = $prefixo . " && " . $command;
                            echo $command . "<br>";
                            $arrCommand[] = $command;

                            $shell = shell_exec($command);
                            echo $shell . "<br>";
                            $arrCommand[] = $shell;
                            // return $arrCommand;
                        }
                        $this->saveLog($arrCommand);
                    }
                }

            } else {
               return "WELCOME TO AUTODEPLOY!";
            }

        } catch (\Exception $e) {
            return ['error'];
        }

    }
    public function gitlab(Request $request)
    {
        try {
            $input = $request->all();

            $config = (new GetConfig)->getConfig();
            if ($request->isMethod('post')) {

                if ($config->get('save_request_body') == true){
                    $this->saveLog($input);
                }

                $branch = $config->get('branch');
                dd($branch, 'controller');

                if (isset($input['ref']) and $input['ref'] == 'refs/heads/' . $branch) {

                    if(count($config->get('commands.servidor'))) {
                        foreach($config->get('commands.servidor') as $command) {

                            $command = str_replace("{branch}", $branch, $command);

                            $prefixo = "cd " . $config->get('folder_git');
                            $command = $prefixo . " && " . $command;
                            echo $command . "<br>";
                            $arrCommand[] = $command;

                            $shell = shell_exec($command);
                            echo $shell . "<br>";
                            $arrCommand[] = $shell;
                            // return $arrCommand;
                        }
                        $this->saveLog($arrCommand);
                    }
                }

            } else {
               return "WELCOME TO AUTODEPLOY!";
            }

        } catch (\Exception $e) {
            return ['error'];
        }

    }

    private function saveLog($input)
    {
        file_put_contents( (new GetConfig)->rootAPP() . '/storage/logs/laravel-'.date('Y-m-d').'.log', "[" . date('Y-m-d H:i:s') . "] local.INFO: AUTODEPLOY ". PHP_EOL . "[stacktrace]" . PHP_EOL . json_encode($input). PHP_EOL, FILE_APPEND);
    }

    public function logs()
    {
        // https://github.com/czproject/git-php
        return (new GitRepository((new GetConfig)->getConfig()->get('folder_git')))->getLog();
    }
}
