<?php
// ============================================================
// FoodExpress — HTTP Response Helpers
// ============================================================
declare(strict_types=1);

function sendJson(mixed $data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function sendSuccess(mixed $data = null, string $message = 'OK', int $code = 200): void
{
    sendJson(['success' => true, 'message' => $message, 'data' => $data], $code);
}

function sendError(int $code, string $message, mixed $errors = null): void
{
    $body = ['success' => false, 'message' => $message];
    if ($errors !== null) $body['errors'] = $errors;
    sendJson($body, $code);
}

function getBody(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '{}', true);
    return is_array($data) ? $data : [];
}

function requireMethod(string ...$methods): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'], $methods, true)) {
        sendError(405, 'Método não permitido');
    }
}
