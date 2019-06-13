<?php
use Illuminate\Routing\Router;
/** @var $router Router */
$router->get('/api/webhookv2', function () {
    return 'hello world!';
});
$router->get('/foo', function () {
    return 'hello world!';
});
$router->get('bye', function () {
    return 'goodbye world!';
});
$router->group(['namespace' => 'App\Controllers', 'prefix' => 'autodeploy'], function (Router $router) {
    $router->get('/webhookv2', function(){
        // die('aqio');
        return 'users';
    });
    $router->get('/users', ['name' => 'users.index', 'uses' => 'UsersController@index']);
    // $router->get('/', ['name' => 'users.index', 'uses' => 'UsersController@index']);
    // $router->post('/', ['name' => 'users.store', 'uses' => 'UsersController@store']);
});
// catch-all route
// $router->any('{any}', function () {
//     return 'four oh four';
// })->where('any', '(.*)');