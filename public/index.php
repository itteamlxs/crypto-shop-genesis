
<?php
/**
 * Main entry point for the application
 * Routes all requests to appropriate controllers
 */

// Load bootstrap configuration
require_once dirname(__DIR__) . '/config/bootstrap.php';

// Include the router
require_once APP_ROOT . '/src/Router.php';
use App\Router;

// Create router instance
$router = new Router();

// Define routes
$router->get('/', 'ProductController@index');
$router->get('/product/{id}', 'ProductController@show');
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->get('/checkout', 'OrderController@checkout');
$router->post('/order/create', 'OrderController@create');
$router->get('/order/status/{id}', 'OrderController@status');
$router->get('/order/success', 'OrderController@success');

// Admin routes - these are handled directly by the PHP files in /public/admin/
// The router doesn't need to handle them since they're accessed directly.
// To access these pages, visit /admin/login.php and /admin/dashboard.php

// Execute the router
$router->resolve();
