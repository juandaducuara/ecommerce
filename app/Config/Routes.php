<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// ─── Auth (solo para guests, excepto logout) ───
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login', 'Web\AuthController::loginForm');
    $routes->post('login', 'Web\AuthController::login');
    $routes->get('register', 'Web\AuthController::registerForm');
    $routes->post('register', 'Web\AuthController::register');
    $routes->get('forgot-password', 'Web\AuthController::forgotPasswordForm');
    $routes->post('forgot-password', 'Web\AuthController::forgotPassword');
    $routes->get('reset-password/(:any)', 'Web\AuthController::resetPasswordForm/$1');
    $routes->post('reset-password', 'Web\AuthController::resetPassword');
});

$routes->get('logout', 'Web\AuthController::logout', ['filter' => 'auth']);

// ─── Catálogo público ───
$routes->get('products', 'Web\CatalogController::index');
$routes->get('products/(:segment)', 'Web\CatalogController::show/$1');
$routes->get('categories/(:segment)', 'Web\CatalogController::category/$1');

// ─── Carrito (público, funciona con sesión) ───
$routes->get('cart', 'Web\CartController::index');
$routes->post('cart/add', 'Web\CartController::add');
$routes->post('cart/update', 'Web\CartController::update');
$routes->post('cart/remove', 'Web\CartController::remove');
$routes->post('cart/coupon', 'Web\CartController::applyCoupon');
$routes->post('cart/coupon/remove', 'Web\CartController::removeCoupon');

// ─── Checkout (requiere autenticación) ───
$routes->group('checkout', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Web\CheckoutController::index');
    $routes->post('/', 'Web\CheckoutController::process');
    $routes->get('success/(:segment)', 'Web\CheckoutController::success/$1');
});

// ─── Account (usuarios autenticados) ───
$routes->group('account', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Web\AccountController::index');
    $routes->get('orders', 'Web\OrderController::index');
    $routes->get('orders/(:segment)', 'Web\OrderController::show/$1');
});

// ─── Admin ───
$routes->group('admin', ['filter' => 'role:super-admin,admin'], static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');

    // Usuarios
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users', 'Admin\UserController::store');
    $routes->get('users/(:num)/edit', 'Admin\UserController::edit/$1');
    $routes->post('users/(:num)', 'Admin\UserController::update/$1');
    $routes->post('users/(:num)/delete', 'Admin\UserController::delete/$1');

    // Productos
    $routes->get('products', 'Admin\ProductController::index');
    $routes->get('products/create', 'Admin\ProductController::create');
    $routes->post('products', 'Admin\ProductController::store');
    $routes->get('products/(:num)/edit', 'Admin\ProductController::edit/$1');
    $routes->post('products/(:num)', 'Admin\ProductController::update/$1');
    $routes->post('products/(:num)/delete', 'Admin\ProductController::delete/$1');
    $routes->post('products/(:num)/images/(:num)/delete', 'Admin\ProductController::deleteImage/$1/$2');

    // Pedidos
    $routes->get('orders', 'Admin\OrderController::index');
    $routes->get('orders/(:num)', 'Admin\OrderController::show/$1');
    $routes->post('orders/(:num)/status', 'Admin\OrderController::updateStatus/$1');
    $routes->post('orders/(:num)/payment-status', 'Admin\OrderController::updatePaymentStatus/$1');
    $routes->post('orders/(:num)/shipments', 'Admin\OrderController::addShipment/$1');
    $routes->post('orders/(:num)/shipments/(:num)/status', 'Admin\OrderController::updateShipmentStatus/$1/$2');
});
