<?php
require_once __DIR__ . '/helpers/env_loader.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/response.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT version()");
    $version = $stmt->fetchColumn();
    
    sendSuccess(['version' => $version], 'Conexão com PostgreSQL (Neon) estabelecida com sucesso!');
} catch (Exception $e) {
    sendError(500, 'Falha na conexão: ' . $e->getMessage());
}
