<?php
// controllers/AuthController.php

declare(strict_types=1);

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // POST /register
    public function register(): void
    {
        $body = get_body();

        $nome  = trim($body['nome']  ?? '');
        $email = trim($body['email'] ?? '');
        $senha = $body['senha'] ?? '';
        $tipo  = $body['tipo']  ?? 'cliente';
        $tel   = trim($body['telefone'] ?? '');

        if (!$nome || !$email || !$senha) {
            json_response(['error' => 'Nome, email e senha são obrigatórios.'], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_response(['error' => 'Email inválido.'], 422);
        }

        if (strlen($senha) < 8) {
            json_response(['error' => 'A senha deve ter pelo menos 8 caracteres.'], 422);
        }

        if (!in_array($tipo, ['cliente', 'restaurante'])) {
            $tipo = 'cliente';
        }

        // Verifica duplicado
        $st = $this->db->prepare('SELECT id FROM utilizadores WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetch()) {
            json_response(['error' => 'Email já registado.'], 409);
        }

        $hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
        $st = $this->db->prepare(
            'INSERT INTO utilizadores (nome, email, telefone, senha, tipo)
             VALUES (?, ?, ?, ?, ?) RETURNING id, nome, email, tipo, criado_em'
        );
        $st->execute([$nome, $email, $tel ?: null, $hash, $tipo]);
        $user = $st->fetch();

        $token = JWT::generate([
            'id'    => $user['id'],
            'email' => $user['email'],
            'tipo'  => $user['tipo'],
            'nome'  => $user['nome'],
        ]);

        // Envia email de boas-vindas via Brevo
        try {
            $emailService = new EmailService();
            $emailService->sendWelcome($user['email'], $user['nome']);
        } catch (Throwable $e) {
            error_log('[AuthController] Erro ao enviar welcome email: ' . $e->getMessage());
        }

        json_response(['token' => $token, 'user' => $user], 201);
    }

    // POST /login
    public function login(): void
    {
        $body  = get_body();
        $email = trim($body['email'] ?? '');
        $senha = $body['senha'] ?? '';

        if (!$email || !$senha) {
            json_response(['error' => 'Email e senha são obrigatórios.'], 422);
        }

        $st = $this->db->prepare(
            'SELECT id, nome, email, senha, tipo, ativo, avatar FROM utilizadores WHERE email = ?'
        );
        $st->execute([$email]);
        $user = $st->fetch();

        if (!$user || !password_verify($senha, $user['senha'])) {
            json_response(['error' => 'Credenciais inválidas.'], 401);
        }

        if (!$user['ativo']) {
            json_response(['error' => 'Conta desativada. Contacte o suporte.'], 403);
        }

        unset($user['senha']);

        $token = JWT::generate([
            'id'    => $user['id'],
            'email' => $user['email'],
            'tipo'  => $user['tipo'],
            'nome'  => $user['nome'],
        ]);

        json_response(['token' => $token, 'user' => $user]);
    }

    // POST /forgot-password
    public function forgotPassword(): void
    {
        $body  = get_body();
        $email = trim($body['email'] ?? '');

        if (!$email) {
            json_response(['error' => 'Email é obrigatório.'], 422);
        }

        $st = $this->db->prepare('SELECT id FROM utilizadores WHERE email = ?');
        $st->execute([$email]);
        $user = $st->fetch();

        // Resposta genérica por segurança
        if (!$user) {
            json_response(['message' => 'Se o email existir, receberá um link de recuperação.']);
        }

        $token  = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $st = $this->db->prepare(
            'UPDATE utilizadores SET reset_token = ?, reset_expira = ? WHERE id = ?'
        );
        $st->execute([$token, $expira, $user['id']]);

        // Envio de email via Brevo
        try {
            $emailService = new EmailService();
            $emailService->sendPasswordReset($email, $token);
        } catch (Throwable $e) {
            error_log('[AuthController] Erro ao enviar reset email: ' . $e->getMessage());
        }

        json_response(['message' => 'Se o email existir, receberá um link de recuperação.']);
    }

    // POST /reset-password
    public function resetPassword(): void
    {
        $body  = get_body();
        $token = trim($body['token'] ?? '');
        $senha = $body['senha'] ?? '';

        if (!$token || !$senha) {
            json_response(['error' => 'Token e nova senha são obrigatórios.'], 422);
        }

        if (strlen($senha) < 8) {
            json_response(['error' => 'A senha deve ter pelo menos 8 caracteres.'], 422);
        }

        $st = $this->db->prepare(
            "SELECT id FROM utilizadores WHERE reset_token = ? AND reset_expira > NOW()"
        );
        $st->execute([$token]);
        $user = $st->fetch();

        if (!$user) {
            json_response(['error' => 'Token inválido ou expirado.'], 400);
        }

        $hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
        $st = $this->db->prepare(
            'UPDATE utilizadores SET senha = ?, reset_token = NULL, reset_expira = NULL WHERE id = ?'
        );
        $st->execute([$hash, $user['id']]);

        json_response(['message' => 'Senha atualizada com sucesso.']);
    }

    // POST /logout
    public function logout(array $user): void
    {
        // JWT é stateless; o cliente descarta o token.
        json_response(['message' => 'Sessão encerrada.']);
    }

    // PUT /me
    public function updateProfile(array $user): void
    {
        $body  = get_body();
        $nome  = trim($body['nome']     ?? '');
        $tel   = trim($body['telefone'] ?? '');

        if (!$nome) {
            json_response(['error' => 'Nome é obrigatório.'], 422);
        }

        $st = $this->db->prepare(
            'UPDATE utilizadores SET nome = ?, telefone = ? WHERE id = ?
             RETURNING id, nome, email, telefone, tipo, avatar, criado_em'
        );
        $st->execute([$nome, $tel ?: null, $user['id']]);

        json_response($st->fetch());
    }
}
