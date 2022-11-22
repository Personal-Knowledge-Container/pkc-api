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

// My entry point for development
$router->get('/mw_info','MediawikiController@mediawiki_getinfo');

// Read page listing by keyword
$router->get('/page', 'MediawikiController@page_listing');

// Read page by Title
$router->get('/page/title', 'MediawikiController@page_by_title');

// Read page by id
$router->get('/page/id', 'MediawikiController@page_by_id');

// Create new page
$router->post('/page/create', 'MediawikiController@create_new_page');

// Product Related
$router->post('/product/create', 'MediawikiController@create_new_product');

// create PO
$router->post('/purchase-order/create', 'MediawikiController@create_new_po');

// create Contract
$router->post('/contract/create', 'MediawikiController@create_new_contract');

// create delivery proof
$router->post('/delivery-proof/create', 'MediawikiController@create_delivery_proof');

// release payment
$router->post('/payment/create', 'MediawikiController@create_release_payment');

// get wiki text
$router->get('/wikitext/get', 'MediawikiController@get_wikitext');