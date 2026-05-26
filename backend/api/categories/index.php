<?php
declare(strict_types=1);

requireMethod('GET');

$db = getDB();
$stmt = $db->query("SELECT DISTINCT nome FROM categorias_pratos ORDER BY nome ASC");
$categoriesData = $stmt->fetchAll();

// Mapear apenas para uma lista de strings contendo os nomes das categorias
$categories = array_map(function($row) {
    return $row['nome'];
}, $categoriesData);

// Adicionar "Todos" ao início
array_unshift($categories, 'Todos');

sendSuccess($categories);
