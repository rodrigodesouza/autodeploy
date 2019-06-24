<?php

namespace Rd7\Autodeploy\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;

class AutodeployController extends Controller
{
   
    public function webhook(Request $request)
    {
        $fileConfig = __DIR__ . '/../../config/config.php';

        if ($request->isMethod('post')) {

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
                Log::info($arrCommand);

            }
        } else {
            echo "get";
        }

    }
}
