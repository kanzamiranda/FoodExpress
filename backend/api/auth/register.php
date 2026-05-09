<?php
declare(strict_types=1);

requireMethod('POST');

$data = getBody();
$db = getDB();

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    sendError(400, 'Todos os campos são obrigatórios');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError(400, 'E-mail inválido');
}

// Check if user exists
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    sendError(409, 'Este e-mail já está em uso');
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Create user
try {
    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword]);
    $userId = (int)$db->lastInsertId();

    sendSuccess(['id' => $userId], 'Utilizador registado com sucesso', 201);
} catch (PDOException $e) {
    sendError(500, 'Erro ao criar conta');
}
