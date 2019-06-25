<?php

namespace Rd7\Autodeploy\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Config\Repository;
// use Illuminate\Support\Facades\Log;

class AutodeployController extends Controller
{
   
    public function webhook(Request $request)
    {
        $fileConfig = __DIR__ . '/../../config/config.php';

        if ($request->isMethod('post')) {

            $input = $request;

            if(file_exists($fileConfig)) {
                $config = new Repository(require $fileConfig);

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
                        }
                    }
                }
                file_put_contents( __DIR__ . '/../../../../../../storage/logs/deploy-'.date('d-m-Y').'.log', date('d/m/Y H:i:s').' '.json_encode($arrCommand). PHP_EOL, FILE_APPEND);
                // Log::info($arrCommand);

            }
        } else {
            echo "get";
        }

    }
}
