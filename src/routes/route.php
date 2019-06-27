<?php
use Illuminate\Routing\Router;

$router->group(['namespace' => 'Rd7\Autodeploy\Http\Controllers', 'prefix' => 'autodeploy'], function (Router $router) {
    $router->any('/recept', ['name' => 'webhook.index', 'uses' => 'AutodeployController@webhook']);
    $router->get('/gitlab', ['name' => 'webhook.index', 'uses' => 'AutodeployController@webhook']);
    $router->get('/logs', ['name' => 'webhook.index', 'uses' => 'AutodeployController@logs']);

});
// catch-all route
// $router->any('{any}', function () {
//     return 'four oh four';
// })->where('any', '(.*)');