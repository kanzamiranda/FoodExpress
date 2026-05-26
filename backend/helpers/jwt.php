<?php
// ============================================================
// FoodExpress — JWT (Pure PHP, no libraries)
// HS256 implementation
// ============================================================
declare(strict_types=1);

define('JWT_SECRET',      getenv('JWT_SECRET')      ?: 'foodexpress_super_secret_change_in_prod');
define('JWT_EXPIRES_IN',  (int)(getenv('JWT_EXPIRES_IN')  ?: 3600));       // 1h
define('JWT_REFRESH_EXP', (int)(getenv('JWT_REFRESH_EXP') ?: 2592000));    // 30 days

// --- Encode ---
function jwtEncode(array $payload): string
{
    $header  = base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64UrlEncode(json_encode($payload));
    $sig     = base64UrlEncode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    return "$header.$payload.$sig";
}

// --- Decode & Verify ---
function jwtDecode(string $token): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header, $payload, $sig] = $parts;
    $expected = base64UrlEncode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));

    if (!hash_equals($expected, $sig)) return null;

    $data = json_decode(base64UrlDecode($payload), true);
    if (!$data || (isset($data['exp']) && $data['exp'] < time())) return null;

    return $data;
}

// --- Create Access Token ---
function createAccessToken(array $user): string
{
    return jwtEncode([
        'sub'   => $user['id'],
        'email' => $user['email'],
        'role'  => $user['role'],
        'iat'   => time(),
        'exp'   => time() + JWT_EXPIRES_IN,
    ]);
}

// --- Create Refresh Token ---
function createRefreshToken($userId): string
{
    return jwtEncode([
        'sub'  => $userId,
        'type' => 'refresh',
        'iat'  => time(),
        'exp'  => time() + JWT_REFRESH_EXP,
    ]);
}

// --- Get authenticated user (or abort) ---
function requireAuth(): array
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!str_starts_with($header, 'Bearer ')) {
        sendError(401, 'Token de autenticação ausente');
    }

    $token = substr($header, 7);
    $data  = jwtDecode($token);

    if ($data === null) sendError(401, 'Token inválido ou expirado');

    return $data;
}

// --- Require admin role ---
function requireAdmin(): array
{
    $auth = requireAuth();
    if ($auth['role'] !== 'admin') sendError(403, 'Acesso negado. Apenas administradores.');
    return $auth;
}

// --- Helpers ---
function base64UrlEncode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string
{
    return base64_decode(strtr($data, '-_', '+/'));
}
