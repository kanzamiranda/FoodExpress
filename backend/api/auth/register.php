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
$stmt = $db->prepare("SELECT id FROM utilizadores WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    sendError(409, 'Este e-mail já está em uso');
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Create user
try {
    $stmt = $db->prepare("INSERT INTO utilizadores (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente') RETURNING id");
    $stmt->execute([$name, $email, $hashedPassword]);
    $userId = $stmt->fetchColumn();

    // Enviar e-mail de boas-vindas assincronamente através do Brevo
    try {
        $emailService = new EmailService();
        $emailService->sendWelcome($email, $name);
    } catch (Throwable $mailEx) {
        error_log("Erro no envio do email de boas-vindas: " . $mailEx->getMessage());
    }

    sendSuccess(['id' => $userId], 'Utilizador registado com sucesso', 201);
} catch (PDOException $e) {
    sendError(500, 'Erro ao criar conta: ' . $e->getMessage());
}
