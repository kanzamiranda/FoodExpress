<?php
declare(strict_types=1);

$auth = requireAuth();
requireMethod('GET');

$db = getDB();

if ($auth['role'] === 'admin') {
    // Admin sees all orders
    $stmt = $db->query("
        SELECT o.*, u.name as user_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC
    ");
} else {
    // Client sees only their orders
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$auth['sub']]);
}

$orders = $stmt->fetchAll();
sendSuccess($orders);
