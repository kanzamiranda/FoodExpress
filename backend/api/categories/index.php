<?php
declare(strict_types=1);

requireMethod('GET');

$db = getDB();
$stmt = $db->query("SELECT * FROM categories WHERE is_active = TRUE ORDER BY sort_order ASC");
$categories = $stmt->fetchAll();

sendSuccess($categories);
