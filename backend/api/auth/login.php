<?php
declare(strict_types=1);

requireMethod('POST');

$data = getBody();
$db = getDB();

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    sendError(400, 'E-mail e senha são obrigatórios');
}

$stmt = $db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND is_active = TRUE");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    sendError(401, 'Credenciais inválidas');
}

// Generate tokens
$accessToken = createAccessToken($user);
$refreshToken = createRefreshToken((int)$user['id']);

// Save refresh token
$stmt = $db->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
$stmt->execute([
    $user['id'],
    $refreshToken,
    date('Y-m-d H:i:s', time() + JWT_REFRESH_EXP)
]);

sendSuccess([
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ],
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken
], 'Login efetuado com sucesso');
