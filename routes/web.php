<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'api/v1/domains'], function() use($router){
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');

    $router->get('/profile', 'UserController@profile');
    $router->get('/users/find/{id}', 'UserController@singleUser');
    $router->get('/users', 'UserController@allUsers');

    $router->get('/', 'DomainController@index');
    $router->post('/create', 'DomainController@create');
    $router->get('/find/{id}', 'DomainController@show');
    $router->put('/update/{id}', 'DomainController@update');
    $router->delete('/delete/{id}', 'DomainController@destroy');

    $router->get('/export', 'DomainController@export_all');
    $router->post('/import', 'DomainController@bulk_import');
    });
