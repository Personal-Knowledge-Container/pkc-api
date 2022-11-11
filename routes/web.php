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

// Read page listing by keyword
$router->get('/page', 'MediawikiController@page_listing');

// Read page by Title
$router->get('/page/title', 'MediawikiController@page_by_title');

// Read page by id
$router->get('/page/id', 'MediawikiController@page_by_id');