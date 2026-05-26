<?php
// ============================================================
// FoodExpress — CORS Configuration
// ============================================================
declare(strict_types=1);

// Origens permitidas — apenas Render (+ localhost para desenvolvimento local)
$frontendUrl = getenv('FRONTEND_URL') ?: '';

$allowed = array_filter([
    'http://localhost:4200',
    'http://localhost:4000',
    $frontendUrl ?: null,           // URL do frontend no Render (via env var)
    'https://foodexpress-frontend.onrender.com',  // fallback explícito
]);

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed, true)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Por defeito permite o localhost em dev
    header('Access-Control-Allow-Origin: http://localhost:4200');
}

header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
