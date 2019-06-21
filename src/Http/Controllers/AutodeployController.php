<?php

namespace Rd7\Autodeploy\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Config\Repository;

class AutodeployController extends Controller
{
   
    public function webhook(Request $request)
    {
        $fileConfig = __DIR__ . '/../../Config/config.php';
        if(file_exists($fileConfig)) {
            // $config = require $fileConfig;

            $config = new Repository(require $fileConfig);
            foreach($config->get('commands.servidor') as $command) {
                dd($command);
            }
            
            dd($config->get('config.name'));


            echo "This is coming from config/app.php: <hr>" . $config->get('config.name') . "<br><br><br>";
            

        }
        // echo include 'config.php';
        dd('aqio');

        dd($v);

        dd(new Repository(), $v);
        $config = new Repository($v);

        dd($config);
        // $config = new Repository(require $configPath . 'config.php');

       // echo "This is coming from config/app.php: <hr>" . $config->get('config.name') . "<br><br><br>";

        dd($request->all());

    }
}
