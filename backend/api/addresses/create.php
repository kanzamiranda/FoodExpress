<?php
declare(strict_types=1);

$auth = requireAuth();
requireMethod('POST');

$data = getBody();
$db = getDB();

$street = trim($data['street'] ?? '');
$city = trim($data['city'] ?? '');
$label = trim($data['label'] ?? 'Casa');
$isDefault = (bool)($data['is_default'] ?? false);

if (empty($street) || empty($city)) {
    sendError(400, 'Rua e cidade são obrigatórios');
}

try {
    $db->beginTransaction();

    if ($isDefault) {
        // Reset other default addresses
        $stmt = $db->prepare("UPDATE addresses SET is_default = FALSE WHERE user_id = ?");
        $stmt->execute([$auth['sub']]);
    }

    $stmt = $db->prepare("
        INSERT INTO addresses (user_id, label, street, city, postal_code, is_default) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $auth['sub'],
        $label,
        $street,
        $city,
        $data['postal_code'] ?? null,
        $isDefault ? 1 : 0
    ]);

    $db->commit();
    sendSuccess(['id' => $db->lastInsertId()], 'Endereço guardado com sucesso', 201);
} catch (PDOException $e) {
    if ($db->inTransaction()) $db->rollBack();
    sendError(500, 'Erro ao salvar endereço');
}
