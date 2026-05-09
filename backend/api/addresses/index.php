<?php
declare(strict_types=1);

$auth = requireAuth();
requireMethod('GET');

$db = getDB();
$stmt = $db->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
$stmt->execute([$auth['sub']]);
$addresses = $stmt->fetchAll();

sendSuccess($addresses);
