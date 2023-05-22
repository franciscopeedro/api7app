<?php

use Illuminate\Support\Facades\Route;

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

Route::post('/cadastro', 'CadastroController@create');
Route::post('/login', 'AuthController@login');

Route::post('/get_otp', 'AuthController@getOTP');
Route::post('/valid_otp', 'AuthController@validarOTP');
