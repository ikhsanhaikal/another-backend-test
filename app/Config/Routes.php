<?php

use App\Controllers\Customer;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', [Customer::class, 'show']);
$routes->post('/customers', [Customer::class, 'create']);
$routes->delete('/customers', [Customer::class, 'delete']);
$routes->get('/customers', [Customer::class, 'index']);
$routes->get('/customers/(:num)', [Customer::class, 'show/$1']);
$routes->put('/customers/(:num)', [Customer::class, 'update/$1']);
