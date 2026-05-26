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

$stmt = $db->prepare("SELECT id, nome as name, email, senha as password, tipo as role FROM utilizadores WHERE email = ? AND ativo = TRUE");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    sendError(401, 'Credenciais inválidas');
}

// Generate tokens
$accessToken = createAccessToken($user);
$refreshToken = createRefreshToken($user['id']);

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
