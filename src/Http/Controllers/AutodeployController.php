<?php

namespace Rd7\Autodeploy\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Config\Repository;

class AutodeployController extends Controller
{
    // public $rootAPP =  __DIR__ . '/../../../../../../'; //Production
    public $rootAPP =  __DIR__ . '/../../../../../'; //Developer

    public function webhook(Request $request)
    {
        $appConfig = $this->rootAPP . 'config/autodeploy.php';
        $packageConfig = __DIR__ . '/../../config/config.php';

        if(file_exists($appConfig)) {
            $fileConfig = $appConfig;
        } else {
            $fileConfig = $packageConfig;
        }

        try {
            $input = $request->all();

            $config = new Repository(require $fileConfig);

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

    private function saveLog($input)
    {
        file_put_contents( $this->rootAPP . 'storage/logs/laravel-'.date('Y-m-d').'.log', "[" . date('Y-m-d H:i:s') . "] local.INFO: AUTODEPLOY ". PHP_EOL . "[stacktrace]" . PHP_EOL . json_encode($input). PHP_EOL, FILE_APPEND);
    }
}
