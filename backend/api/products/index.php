<?php
declare(strict_types=1);

requireMethod('GET');

$db = getDB();
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.is_available = TRUE";

$params = [];
if ($categoryId) {
    $query .= " AND p.category_id = ?";
    $params[] = $categoryId;
}

$query .= " ORDER BY p.category_id ASC, p.name ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

sendSuccess($products);
