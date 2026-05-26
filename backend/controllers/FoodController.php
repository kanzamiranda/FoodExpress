<?php
// controllers/FoodController.php

declare(strict_types=1);

class FoodController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // GET /foods — listagem pública com filtros
    public function index(): void
    {
        $search        = trim($_GET['search']        ?? '');
        $restauranteId = trim($_GET['restaurante_id'] ?? '');
        $categoriaId   = trim($_GET['categoria_id']   ?? '');
        $page          = max(1, (int)($_GET['page']  ?? 1));
        $limit         = min(50, max(1, (int)($_GET['limit'] ?? 20)));
        $offset        = ($page - 1) * $limit;

        $where  = ['p.disponivel = TRUE', 'r.ativo = TRUE'];
        $params = [];

        if ($search) {
            $where[]  = '(p.nome ILIKE ? OR p.descricao ILIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($restauranteId) {
            $where[]  = 'p.restaurante_id = ?';
            $params[] = $restauranteId;
        }
        if ($categoriaId) {
            $where[]  = 'p.categoria_id = ?';
            $params[] = $categoriaId;
        }

        $whereStr = implode(' AND ', $where);

        $count = $this->db->prepare("SELECT COUNT(*) FROM pratos p JOIN restaurantes r ON r.id = p.restaurante_id WHERE {$whereStr}");
        $count->execute($params);
        $total = (int)$count->fetchColumn();

        $st = $this->db->prepare(
            "SELECT p.id, p.nome, p.descricao, p.preco, p.imagem, p.destaque,
                    p.restaurante_id, r.nome AS restaurante_nome,
                    c.nome AS categoria_nome
             FROM pratos p
             JOIN restaurantes r ON r.id = p.restaurante_id
             LEFT JOIN categorias_pratos c ON c.id = p.categoria_id
             WHERE {$whereStr}
             ORDER BY p.destaque DESC, p.nome
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

    // GET /restaurants/:id/foods
    public function byRestaurant(string $restaurantId): void
    {
        $st = $this->db->prepare(
            'SELECT p.*, c.nome AS categoria_nome
             FROM pratos p
             LEFT JOIN categorias_pratos c ON c.id = p.categoria_id
             WHERE p.restaurante_id = ?
             ORDER BY c.ordem NULLS LAST, p.nome'
        );
        $st->execute([$restaurantId]);
        json_response($st->fetchAll());
    }

    // POST /foods
    public function store(array $user): void
    {
        AuthMiddleware::requireRole($user, 'restaurante', 'admin');

        $body = get_body();
        $nome = trim($body['nome'] ?? '');
        $preco = $body['preco'] ?? null;
        $restauranteId = $body['restaurante_id'] ?? null;

        if (!$nome || $preco === null || !$restauranteId) {
            json_response(['error' => 'Nome, preço e restaurante_id são obrigatórios.'], 422);
        }

        // Verificar propriedade
        if ($user['tipo'] !== 'admin') {
            $st = $this->db->prepare('SELECT id FROM restaurantes WHERE id = ? AND utilizador_id = ?');
            $st->execute([$restauranteId, $user['id']]);
            if (!$st->fetch()) json_response(['error' => 'Sem permissão neste restaurante.'], 403);
        }

        $st = $this->db->prepare(
            'INSERT INTO pratos
                (restaurante_id, categoria_id, nome, descricao, preco, imagem, disponivel, destaque)
             VALUES (?,?,?,?,?,?,?,?)
             RETURNING *'
        );
        $st->execute([
            $restauranteId,
            $body['categoria_id'] ?? null,
            $nome,
            $body['descricao']    ?? null,
            (float)$preco,
            $body['imagem']       ?? null,
            isset($body['disponivel']) ? ($body['disponivel'] ? 'true' : 'false') : 'true',
            isset($body['destaque'])   ? ($body['destaque']   ? 'true' : 'false') : 'false',
        ]);

        json_response($st->fetch(), 201);
    }

    // PUT /foods/:id
    public function update(string $id, array $user): void
    {
        AuthMiddleware::requireRole($user, 'restaurante', 'admin');

        $prato = $this->getOwnFood($id, $user);
        $body  = get_body();

        $st = $this->db->prepare(
            'UPDATE pratos SET
                nome        = COALESCE(?, nome),
                descricao   = COALESCE(?, descricao),
                preco       = COALESCE(?, preco),
                imagem      = COALESCE(?, imagem),
                categoria_id= COALESCE(?, categoria_id),
                disponivel  = COALESCE(?, disponivel),
                destaque    = COALESCE(?, destaque)
             WHERE id = ?
             RETURNING *'
        );
        $st->execute([
            $body['nome']        ?? null,
            $body['descricao']   ?? null,
            isset($body['preco']) ? (float)$body['preco'] : null,
            $body['imagem']      ?? null,
            $body['categoria_id'] ?? null,
            isset($body['disponivel']) ? ($body['disponivel'] ? 'true' : 'false') : null,
            isset($body['destaque'])   ? ($body['destaque']   ? 'true' : 'false') : null,
            $id,
        ]);

        json_response($st->fetch());
    }

    // DELETE /foods/:id
    public function destroy(string $id, array $user): void
    {
        AuthMiddleware::requireRole($user, 'restaurante', 'admin');

        $this->getOwnFood($id, $user);

        $st = $this->db->prepare('DELETE FROM pratos WHERE id = ?');
        $st->execute([$id]);

        json_response(['message' => 'Prato removido.']);
    }

    private function getOwnFood(string $id, array $user): array
    {
        $st = $this->db->prepare(
            'SELECT p.*, r.utilizador_id FROM pratos p
             JOIN restaurantes r ON r.id = p.restaurante_id
             WHERE p.id = ?'
        );
        $st->execute([$id]);
        $prato = $st->fetch();

        if (!$prato) json_response(['error' => 'Prato não encontrado.'], 404);

        if ($user['tipo'] !== 'admin' && $prato['utilizador_id'] !== $user['id']) {
            json_response(['error' => 'Sem permissão.'], 403);
        }

        return $prato;
    }
}
