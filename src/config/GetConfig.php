<?php

namespace Rd7\Autodeploy\Config;

use Illuminate\Config\Repository;

class GetConfig
{
    // public $rootAPP =  __DIR__ . '/../../../../'; //Developer
    public $rootAPP =  __DIR__ . '/../../../../../'; //Production

    public function getConfig()
    {
        $appConfig = $this->rootAPP . 'config/autodeploy.php';
        $packageConfig = __DIR__ . '/config.php';

        if(file_exists($appConfig)) {
            $fileConfig = $appConfig;
        } else {
            $fileConfig = $packageConfig;
        }

        $config = new Repository(require $fileConfig);

        return $config;

    }
}