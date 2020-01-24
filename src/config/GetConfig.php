<?php

namespace Rd7\Autodeploy\Config;

use Illuminate\Config\Repository;

class GetConfig
{
    public function getConfig()
    {
        $appConfig = $this->rootAPP() . '/config/autodeploy.php';
        $packageConfig = __DIR__ . '/config.php';

        if(file_exists($appConfig)) {
            $fileConfig = $appConfig;
        } else {
            $fileConfig = $packageConfig;
        }

        $config = new Repository(require $fileConfig);

        return $config;

    }

    public function rootAPP()
    {
        // return realpath(__DIR__ . '/../../../../'); //Developer
        return realpath(__DIR__ . '/../../../../../'); //Production
    }
}
