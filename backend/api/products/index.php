<?php
declare(strict_types=1);

requireMethod('GET');

$db = getDB();
$categoryName = isset($_GET['category']) ? trim($_GET['category']) : null;

$query = "SELECT 
            p.id, 
            p.nome as name, 
            p.descricao as description, 
            p.preco::float as price, 
            p.imagem as image, 
            c.nome as category,
            CASE 
                WHEN p.destaque = TRUE THEN 'Popular' 
                ELSE NULL 
            END as badge,
            4.8 as rating,
            '25 min' as \"prepTime\",
            CASE c.nome
                WHEN 'Pizza' THEN '🍕'
                WHEN 'Burgers' THEN '🍔'
                WHEN 'Massas' THEN '🍝'
                WHEN 'Saladas' THEN '🥗'
                WHEN 'Sobremesas' THEN '🍰'
                WHEN 'Bebidas' THEN '🥤'
                ELSE '🍽️'
            END as emoji
          FROM pratos p 
          JOIN categorias_pratos c ON p.categoria_id = c.id 
          WHERE p.disponivel = TRUE";

$params = [];
if ($categoryName && $categoryName !== 'Todos') {
    $query .= " AND c.nome = ?";
    $params[] = $categoryName;
}

$query .= " ORDER BY c.ordem ASC, p.nome ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

sendSuccess($products);
