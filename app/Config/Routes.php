<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

// Default route
$routes->get('/', 'Home::index');

// Shopify OAuth Routes
$routes->get('shopify/auth', 'ShopifyAuth::authorize'); // Initiates the OAuth flow
$routes->get('shopify/callback', 'ShopifyAuth::callback'); // Handles Shopify callback

// Products route
$routes->get('shopify/products', 'ShopifyAuth::products'); // Displays products

// Other routes
