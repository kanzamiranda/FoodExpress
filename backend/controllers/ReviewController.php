<?php
// controllers/ReviewController.php

declare(strict_types=1);

class ReviewController
{
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    // POST /reviews
    public function store(array $user): void
    {
        AuthMiddleware::requireRole($user, 'cliente');
        $body = get_body();

        $restauranteId = $body['restaurante_id'] ?? null;
        $pedidoId      = $body['pedido_id']      ?? null;
        $nota          = (int)($body['nota']     ?? 0);
        $comentario    = trim($body['comentario'] ?? '');

        if (!$restauranteId || !$nota || $nota < 1 || $nota > 5) {
            json_response(['error' => 'restaurante_id e nota (1-5) são obrigatórios.'], 422);
        }

        // Verificar se pedido pertence ao utilizador e foi entregue
        if ($pedidoId) {
            $st = $this->db->prepare(
                "SELECT id FROM pedidos WHERE id=? AND utilizador_id=? AND status='entregue'"
            );
            $st->execute([$pedidoId, $user['id']]);
            if (!$st->fetch()) {
                json_response(['error' => 'Pedido inválido para avaliação.'], 400);
            }
        }

        $st = $this->db->prepare(
            'INSERT INTO avaliacoes (utilizador_id, restaurante_id, pedido_id, nota, comentario)
             VALUES (?,?,?,?,?)
             ON CONFLICT (pedido_id) DO UPDATE SET nota=EXCLUDED.nota, comentario=EXCLUDED.comentario
             RETURNING *'
        );
        $st->execute([$user['id'], $restauranteId, $pedidoId ?: null, $nota, $comentario ?: null]);

        json_response($st->fetch(), 201);
    }

    // GET /restaurants/:id/reviews
    public function byRestaurant(string $id): void
    {
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = min(50, max(1, (int)($_GET['limit'] ?? 10)));
        $offset = ($page - 1) * $limit;

        $count = $this->db->prepare('SELECT COUNT(*) FROM avaliacoes WHERE restaurante_id = ?');
        $count->execute([$id]);
        $total = (int)$count->fetchColumn();

        $st = $this->db->prepare(
            'SELECT a.*, u.nome AS cliente_nome, u.avatar AS cliente_avatar
             FROM avaliacoes a JOIN utilizadores u ON u.id = a.utilizador_id
             WHERE a.restaurante_id = ?
             ORDER BY a.criado_em DESC
             LIMIT ? OFFSET ?'
        );
        $st->execute([$id, $limit, $offset]);

        json_response([
            'data'       => $st->fetchAll(),
            'total'      => $total,
            'page'       => $page,
            'totalPages' => (int)ceil($total / $limit),
        ]);
    }
}
