<?php

use App\Controllers\Customer;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', [Customer::class, 'show']);
$routes->post('customers', [Customer::class, 'create']);
$routes->delete('customers', [Customer::class, 'delete']);
$routes->get('customers', [Customer::class, 'index']);
$routes->get('customers/(:num)', [Customer::class, 'show/$1']);
$routes->put('customers/(:num)', [Customer::class, 'update/$1']);

$routes->get('playground', 'Customer::playground'); // playground endpoint

$routes->post('accounts/(:num)/deposit', 'Account::deposit/$1');
$routes->post('accounts/(:num)/withdraw', 'Account::deposit/$1');
$routes->get('accounts/(:num)', 'Account::show/$1');
$routes->put('accounts/(:num)', 'Account::update/$1');

$routes->post('customers/(:num)/accounts', 'Account::create/$1');
$routes->delete('accounts/(:num)', 'Account::delete/$1');