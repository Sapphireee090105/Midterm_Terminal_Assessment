<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// Authentication Routes
$routes->post('register', 'AuthController::register');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

$routes->group('api', ['filter' => 'auth'], function($routes) {
    $routes->resource('tasks', ['controller' => 'TaskController']);
});