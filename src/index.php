<?php
/**
* Laravel Autodeploy
*
* @source https://github.com/rodrigodesouza/autodeploy
* @contributor Rodrigo de Souza
*
* Configurações obrigatórias:
* No arquivo composer.json da raiz coloque:
*  "autoload": {
*        "files": [
*             "vendor/rd7/autodeploy/src/index.php"
*        ]
*    },
* Fim
* Caminho do autoload.php em produção:
* require_once __DIR__ . '/../../../autoload.php';
*/

// require_once __DIR__ . '/../../../vendor/autoload.php'; //Develop
require_once __DIR__ . '/../../../autoload.php'; // Publicar

use Illuminate\Console\Application;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Routing\Route;
use Illuminate\Routing\UrlGenerator;
// Create a service container
$container = new Container;
// Create a request from server variables, and bind it to the container; optional
$request = Request::capture();

$container->instance('Illuminate\Http\Request', $request);
// Using Illuminate/Events/Dispatcher here (not required); any implementation of
// Illuminate/Contracts/Event/Dispatcher is acceptable
$events = new Dispatcher($container);
// Create the router instance
$router = new Router($events, $container);
// Load the routes
require_once __DIR__ . '/routes/route.php';

//require_once 'commands/HelloWord.php';
// Create the redirect instance

//$artisan = new Application($container, $events, 'Version 1');
// $artisan->setName('My Console App Name');

// Bind a command
// $artisan->resolve(HelloWorld::class);

$redirect = new Redirector(new UrlGenerator($router->getRoutes(), $request));
// use redirect
// return $redirect->home();
// return $redirect->back();
// return $redirect->to('/');
// Dispatch the request through the router
try {
    //code...
    $response = $router->dispatch($request);
    // Send the response back to the browser
    // $artisan->run();
    $response->send();
    exit;
} catch (\Throwable $th) {
    //throw $th;
}
