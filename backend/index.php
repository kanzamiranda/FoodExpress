<?php
// ============================================================
// FoodExpress Backend — Entry Point / Router
// Pure PHP REST API
// ============================================================

declare(strict_types=1);

require_once __DIR__ . '/helpers/env_loader.php';
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/response.php';
require_once __DIR__ . '/helpers/jwt.php';
require_once __DIR__ . '/services/EmailService.php';

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = trim(str_replace('/api', '', $uri), '/');
$parts  = explode('/', $uri);

$resource = $parts[0] ?? '';
$id       = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : null;
$sub      = $parts[2] ?? null;

// Route table
try {
    match (true) {
        // AUTH
        $resource === 'auth' && $id === null && $parts[1] === 'register' && $method === 'POST'
            => require __DIR__ . '/api/auth/register.php',

        $resource === 'auth' && $id === null && $parts[1] === 'login' && $method === 'POST'
            => require __DIR__ . '/api/auth/login.php',

        $resource === 'auth' && $id === null && $parts[1] === 'logout' && $method === 'POST'
            => require __DIR__ . '/api/auth/logout.php',

        $resource === 'auth' && $id === null && $parts[1] === 'refresh' && $method === 'POST'
            => require __DIR__ . '/api/auth/refresh.php',

        // CATEGORIES
        $resource === 'categories' && $method === 'GET' && $id === null
            => require __DIR__ . '/api/categories/index.php',

        // PRODUCTS
        $resource === 'products' && $method === 'GET' && $id === null
            => require __DIR__ . '/api/products/index.php',

        $resource === 'products' && $method === 'GET' && $id !== null
            => require __DIR__ . '/api/products/show.php',

        $resource === 'products' && $method === 'POST' && $id === null
            => require __DIR__ . '/api/products/create.php',

        $resource === 'products' && $method === 'PUT' && $id !== null
            => require __DIR__ . '/api/products/update.php',

        $resource === 'products' && $method === 'DELETE' && $id !== null
            => require __DIR__ . '/api/products/delete.php',

        // ORDERS
        $resource === 'orders' && $method === 'GET' && $id === null
            => require __DIR__ . '/api/orders/index.php',

        $resource === 'orders' && $method === 'GET' && $id !== null
            => require __DIR__ . '/api/orders/show.php',

        $resource === 'orders' && $method === 'POST' && $id === null
            => require __DIR__ . '/api/orders/create.php',

        $resource === 'orders' && $method === 'PATCH' && $id !== null && $sub === 'status'
            => require __DIR__ . '/api/orders/status.php',

        $resource === 'orders' && $method === 'DELETE' && $id !== null
            => require __DIR__ . '/api/orders/cancel.php',

        // ADDRESSES
        $resource === 'addresses' && $method === 'GET'
            => require __DIR__ . '/api/addresses/index.php',

        $resource === 'addresses' && $method === 'POST'
            => require __DIR__ . '/api/addresses/create.php',

        $resource === 'addresses' && $method === 'PUT' && $id !== null
            => require __DIR__ . '/api/addresses/update.php',

        $resource === 'addresses' && $method === 'DELETE' && $id !== null
            => require __DIR__ . '/api/addresses/delete.php',

        // USERS (admin)
        $resource === 'users' && $method === 'GET'
            => require __DIR__ . '/api/users/index.php',

        $resource === 'users' && $method === 'GET' && $parts[1] === 'me'
            => require __DIR__ . '/api/users/me.php',

        default => sendError(404, 'Endpoint não encontrado')
    };
} catch (Throwable $e) {
    sendError(500, 'Erro interno: ' . $e->getMessage());
}
