<?php
declare(strict_types=1);

$auth = requireAuth();
requireMethod('POST');

$data = getBody();
$db = getDB();

$addressId = $data['address_id'] ?? null;
$paymentMethod = $data['payment_method'] ?? 'cash';
$items = $data['items'] ?? [];
$notes = $data['notes'] ?? '';

if (empty($items)) {
    sendError(400, 'O carrinho está vazio');
}

try {
    $db->beginTransaction();

    $subtotal = 0;
    $orderItems = [];

    // Validate products and calculate subtotal
    foreach ($items as $item) {
        $productId = (int)($item['product_id'] ?? 0);
        $qty = (int)($item['quantity'] ?? 1);

        $stmt = $db->prepare("SELECT id, price FROM products WHERE id = ? AND is_available = TRUE");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception("Produto ID $productId não disponível");
        }

        $itemSubtotal = $product['price'] * $qty;
        $subtotal += $itemSubtotal;

        $orderItems[] = [
            'product_id' => $productId,
            'quantity' => $qty,
            'unit_price' => $product['price'],
            'subtotal' => $itemSubtotal
        ];
    }

    $deliveryFee = $subtotal > 0 ? 2.50 : 0;
    $total = $subtotal + $deliveryFee;

    // Create order
    $stmt = $db->prepare("
        INSERT INTO orders (user_id, address_id, payment_method, subtotal, delivery_fee, total, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $auth['sub'],
        $addressId,
        $paymentMethod,
        $subtotal,
        $deliveryFee,
        $total,
        $notes
    ]);
    
    $orderId = (int)$db->lastInsertId();

    // Create order items
    $stmt = $db->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) 
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($orderItems as $item) {
        $stmt->execute([
            $orderId,
            $item['product_id'],
            $item['quantity'],
            $item['unit_price'],
            $item['subtotal']
        ]);
    }

    $db->commit();
    sendSuccess(['order_id' => $orderId], 'Pedido realizado com sucesso', 201);

} catch (Throwable $e) {
    if ($db->inTransaction()) $db->rollBack();
    sendError(500, 'Erro ao processar pedido: ' . $e->getMessage());
}
