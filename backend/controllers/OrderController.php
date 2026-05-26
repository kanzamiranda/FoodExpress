<?php
// controllers/OrderController.php

declare(strict_types=1);

class OrderController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // POST /orders
    public function store(array $user): void
    {
        AuthMiddleware::requireRole($user, 'cliente');

        $body  = get_body();
        $items = $body['items'] ?? [];

        if (empty($items)) {
            json_response(['error' => 'O carrinho está vazio.'], 422);
        }

        $restaurantId = $body['restaurante_id'] ?? null;
        if (!$restaurantId) {
            json_response(['error' => 'Restaurante é obrigatório.'], 422);
        }

        // Verificar se restaurante existe e está aberto
        $st = $this->db->prepare(
            'SELECT id, taxa_entrega, aberto FROM restaurantes WHERE id = ? AND ativo = TRUE'
        );
        $st->execute([$restaurantId]);
        $restaurant = $st->fetch();

        if (!$restaurant) {
            json_response(['error' => 'Restaurante não encontrado.'], 404);
        }
        if (!$restaurant['aberto']) {
            json_response(['error' => 'Restaurante está fechado.'], 400);
        }

        // Calcular total verificando preços reais na BD
        $total      = 0;
        $orderItems = [];

        foreach ($items as $item) {
            $pratoId  = $item['prato_id']  ?? null;
            $qtd      = (int)($item['quantidade'] ?? 1);

            if (!$pratoId || $qtd < 1) continue;

            $st = $this->db->prepare(
                'SELECT id, nome, preco, disponivel, restaurante_id FROM pratos WHERE id = ?'
            );
            $st->execute([$pratoId]);
            $prato = $st->fetch();

            if (!$prato || !$prato['disponivel']) {
                json_response(['error' => "Prato '{$pratoId}' indisponível."], 400);
            }

            if ($prato['restaurante_id'] !== $restaurantId) {
                json_response(['error' => 'Itens de restaurantes diferentes no mesmo pedido.'], 400);
            }

            $subtotal    = round((float)$prato['preco'] * $qtd, 2);
            $total      += $subtotal;
            $orderItems[] = [
                'prato_id'   => $prato['id'],
                'nome_prato' => $prato['nome'],
                'preco_unit' => $prato['preco'],
                'quantidade' => $qtd,
                'subtotal'   => $subtotal,
            ];
        }

        if (empty($orderItems)) {
            json_response(['error' => 'Nenhum item válido encontrado.'], 422);
        }

        $taxaEntrega = (float)$restaurant['taxa_entrega'];
        $totalFinal  = round($total + $taxaEntrega, 2);

        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare(
                'INSERT INTO pedidos
                    (utilizador_id, restaurante_id, endereco_entrega_id,
                     total, taxa_entrega, notas, metodo_pagamento)
                 VALUES (?,?,?,?,?,?,?)
                 RETURNING *'
            );
            $st->execute([
                $user['id'],
                $restaurantId,
                $body['endereco_id']       ?? null,
                $totalFinal,
                $taxaEntrega,
                $body['notas']             ?? null,
                $body['metodo_pagamento']  ?? 'dinheiro',
            ]);
            $order = $st->fetch();

            $stItem = $this->db->prepare(
                'INSERT INTO itens_pedido
                    (pedido_id, prato_id, nome_prato, preco_unit, quantidade, subtotal)
                 VALUES (?,?,?,?,?,?)'
            );
            foreach ($orderItems as $item) {
                $stItem->execute([
                    $order['id'],
                    $item['prato_id'],
                    $item['nome_prato'],
                    $item['preco_unit'],
                    $item['quantidade'],
                    $item['subtotal'],
                ]);
            }

            // Notificação para o restaurante
            $stRest = $this->db->prepare(
                'SELECT utilizador_id FROM restaurantes WHERE id = ?'
            );
            $stRest->execute([$restaurantId]);
            $ownerRow = $stRest->fetch();

            if ($ownerRow) {
                $stNotif = $this->db->prepare(
                    'INSERT INTO notificacoes (utilizador_id, titulo, mensagem, tipo)
                     VALUES (?,?,?,?)'
                );
                $stNotif->execute([
                    $ownerRow['utilizador_id'],
                    'Novo Pedido Recebido!',
                    "Pedido #{$order['id']} no valor de €{$totalFinal} foi recebido.",
                    'pedido',
                ]);
            }

            $this->db->commit();

            // Email de confirmação ao cliente
            try {
                $emailService = new EmailService();
                $emailService->sendOrderConfirmation(
                    $user['email'],
                    $user['nome'],
                    $order['id'],
                    (float)$totalFinal
                );
            } catch (\Throwable $e) {
                error_log('[OrderController] Erro ao enviar email de confirmação: ' . $e->getMessage());
            }

            $order['itens'] = $orderItems;
            json_response($order, 201);

        } catch (\Throwable $e) {
            $this->db->rollBack();
            json_response(['error' => 'Erro ao criar pedido: ' . $e->getMessage()], 500);
        }
    }

    // GET /orders
    public function index(array $user): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = min(50, max(1, (int)($_GET['limit'] ?? 10)));
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? null;

        if ($user['tipo'] === 'cliente') {
            $where  = 'p.utilizador_id = ?';
            $params = [$user['id']];
        } elseif ($user['tipo'] === 'restaurante') {
            // Encontra o restaurante do owner
            $st = $this->db->prepare('SELECT id FROM restaurantes WHERE utilizador_id = ? AND ativo = TRUE LIMIT 1');
            $st->execute([$user['id']]);
            $rest = $st->fetch();
            if (!$rest) json_response(['data' => [], 'total' => 0, 'page' => 1, 'totalPages' => 0]);
            $where  = 'p.restaurante_id = ?';
            $params = [$rest['id']];
        } else {
            // admin vê tudo
            $where  = '1=1';
            $params = [];
        }

        if ($status) {
            $where   .= ' AND p.status = ?';
            $params[] = $status;
        }

        $countSt = $this->db->prepare("SELECT COUNT(*) FROM pedidos p WHERE {$where}");
        $countSt->execute($params);
        $total = (int)$countSt->fetchColumn();

        $st = $this->db->prepare(
            "SELECT p.*, r.nome AS restaurante_nome, r.imagem AS restaurante_imagem,
                    u.nome AS cliente_nome
             FROM pedidos p
             JOIN restaurantes r ON r.id = p.restaurante_id
             JOIN utilizadores u ON u.id = p.utilizador_id
             WHERE {$where}
             ORDER BY p.criado_em DESC
             LIMIT {$limit} OFFSET {$offset}"
        );
        $st->execute($params);

        json_response([
            'data'       => $st->fetchAll(),
            'total'      => $total,
            'page'       => $page,
            'totalPages' => (int)ceil($total / $limit),
        ]);
    }

    // GET /orders/:id
    public function show(string $id, array $user): void
    {
        $st = $this->db->prepare(
            'SELECT p.*, r.nome AS restaurante_nome, r.imagem AS restaurante_imagem,
                    u.nome AS cliente_nome, u.telefone AS cliente_telefone
             FROM pedidos p
             JOIN restaurantes r ON r.id = p.restaurante_id
             JOIN utilizadores u ON u.id = p.utilizador_id
             WHERE p.id = ?'
        );
        $st->execute([$id]);
        $order = $st->fetch();

        if (!$order) json_response(['error' => 'Pedido não encontrado.'], 404);

        // Autorização
        if ($user['tipo'] === 'cliente' && $order['utilizador_id'] !== $user['id']) {
            json_response(['error' => 'Sem permissão.'], 403);
        }

        // Itens do pedido
        $st = $this->db->prepare(
            'SELECT ip.*, p.imagem AS prato_imagem
             FROM itens_pedido ip
             LEFT JOIN pratos p ON p.id = ip.prato_id
             WHERE ip.pedido_id = ?'
        );
        $st->execute([$id]);
        $order['itens'] = $st->fetchAll();

        json_response($order);
    }

    // PUT /orders/:id/status
    public function updateStatus(string $id, array $user): void
    {
        AuthMiddleware::requireRole($user, 'restaurante', 'admin');

        $body      = get_body();
        $newStatus = $body['status'] ?? '';

        $valid = ['recebido', 'a_preparar', 'a_caminho', 'entregue', 'cancelado'];
        if (!in_array($newStatus, $valid)) {
            json_response(['error' => 'Status inválido.'], 422);
        }

        $st = $this->db->prepare('SELECT * FROM pedidos WHERE id = ?');
        $st->execute([$id]);
        $order = $st->fetch();
        if (!$order) json_response(['error' => 'Pedido não encontrado.'], 404);

        // Restaurante só pode alterar os seus pedidos
        if ($user['tipo'] === 'restaurante') {
            $st2 = $this->db->prepare('SELECT utilizador_id FROM restaurantes WHERE id = ?');
            $st2->execute([$order['restaurante_id']]);
            $rest = $st2->fetch();
            if (!$rest || $rest['utilizador_id'] !== $user['id']) {
                json_response(['error' => 'Sem permissão.'], 403);
            }
        }

        $st = $this->db->prepare(
            'UPDATE pedidos SET status = ? WHERE id = ? RETURNING *'
        );
        $st->execute([$newStatus, $id]);

        // Notificar o cliente
        $stNotif = $this->db->prepare(
            'INSERT INTO notificacoes (utilizador_id, titulo, mensagem, tipo) VALUES (?,?,?,?)'
        );
        $labels = [
            'a_preparar' => 'O teu pedido está a ser preparado! 🍳',
            'a_caminho'  => 'O teu pedido está a caminho! 🛵',
            'entregue'   => 'Pedido entregue! Bom apetite 🎉',
            'cancelado'  => 'O teu pedido foi cancelado.',
        ];
        if (isset($labels[$newStatus])) {
            $stNotif->execute([
                $order['utilizador_id'],
                $labels[$newStatus],
                "Pedido #{$id}",
                'status',
            ]);
        }

        json_response($st->fetch());
    }

    // PUT /orders/:id/cancel
    public function cancel(string $id, array $user): void
    {
        $st = $this->db->prepare('SELECT * FROM pedidos WHERE id = ? AND utilizador_id = ?');
        $st->execute([$id, $user['id']]);
        $order = $st->fetch();

        if (!$order) json_response(['error' => 'Pedido não encontrado.'], 404);

        if (!in_array($order['status'], ['recebido'])) {
            json_response(['error' => 'Pedido não pode ser cancelado neste estado.'], 400);
        }

        $st = $this->db->prepare('UPDATE pedidos SET status = ? WHERE id = ? RETURNING *');
        $st->execute(['cancelado', $id]);

        json_response($st->fetch());
    }
}
