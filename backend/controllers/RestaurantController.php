<?php
// controllers/RestaurantController.php

declare(strict_types=1);

class RestaurantController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // GET /restaurants
    public function index(): void
    {
        $search   = trim($_GET['search']   ?? '');
        $category = trim($_GET['category'] ?? '');
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $limit    = min(50, max(1, (int)($_GET['limit'] ?? 12)));
        $offset   = ($page - 1) * $limit;

        $where  = ['r.ativo = TRUE'];
        $params = [];

        if ($search) {
            $where[]  = '(r.nome ILIKE ? OR r.descricao ILIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($category) {
            $where[]  = 'r.categoria ILIKE ?';
            $params[] = "%{$category}%";
        }

        $whereStr = implode(' AND ', $where);

        $count = $this->db->prepare("SELECT COUNT(*) FROM restaurantes r WHERE {$whereStr}");
        $count->execute($params);
        $total = (int)$count->fetchColumn();

        $st = $this->db->prepare(
            "SELECT r.id, r.nome, r.descricao, r.endereco, r.cidade, r.imagem,
                    r.categoria, r.taxa_entrega, r.tempo_entrega,
                    r.avaliacao_media, r.total_avaliacoes, r.aberto
             FROM restaurantes r
             WHERE {$whereStr}
             ORDER BY r.avaliacao_media DESC
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

    // GET /restaurants/:id
    public function show(string $id): void
    {
        $st = $this->db->prepare(
            'SELECT r.*, u.email AS owner_email
             FROM restaurantes r
             JOIN utilizadores u ON u.id = r.utilizador_id
             WHERE r.id = ?'
        );
        $st->execute([$id]);
        $restaurant = $st->fetch();

        if (!$restaurant) {
            json_response(['error' => 'Restaurante não encontrado.'], 404);
        }

        // Categorias e pratos
        $st = $this->db->prepare(
            'SELECT c.id, c.nome, c.ordem,
                    COALESCE(json_agg(
                        json_build_object(
                            \'id\', p.id, \'nome\', p.nome,
                            \'descricao\', p.descricao, \'preco\', p.preco,
                            \'imagem\', p.imagem, \'disponivel\', p.disponivel,
                            \'destaque\', p.destaque
                        ) ORDER BY p.nome
                    ) FILTER (WHERE p.id IS NOT NULL), \'[]\') AS pratos
             FROM categorias_pratos c
             LEFT JOIN pratos p ON p.categoria_id = c.id AND p.disponivel = TRUE
             WHERE c.restaurante_id = ?
             GROUP BY c.id, c.nome, c.ordem
             ORDER BY c.ordem'
        );
        $st->execute([$id]);
        $categories = $st->fetchAll();

        foreach ($categories as &$cat) {
            $cat['pratos'] = json_decode($cat['pratos'], true);
        }

        $restaurant['menu'] = $categories;

        json_response($restaurant);
    }

    // POST /restaurants
    public function store(array $user): void
    {
        AuthMiddleware::requireRole($user, 'restaurante', 'admin');

        $body = get_body();
        $nome = trim($body['nome'] ?? '');
        $end  = trim($body['endereco'] ?? '');

        if (!$nome || !$end) {
            json_response(['error' => 'Nome e endereço são obrigatórios.'], 422);
        }

        $st = $this->db->prepare(
            'INSERT INTO restaurantes
                (utilizador_id, nome, descricao, endereco, cidade, telefone, email,
                 imagem, banner, categoria, taxa_entrega, tempo_entrega)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
             RETURNING *'
        );
        $st->execute([
            $user['id'],
            $nome,
            $body['descricao']    ?? null,
            $end,
            $body['cidade']       ?? null,
            $body['telefone']     ?? null,
            $body['email']        ?? null,
            $body['imagem']       ?? null,
            $body['banner']       ?? null,
            $body['categoria']    ?? null,
            $body['taxa_entrega'] ?? 0,
            $body['tempo_entrega'] ?? null,
        ]);

        json_response($st->fetch(), 201);
    }

    // PUT /restaurants/:id
    public function update(string $id, array $user): void
    {
        $restaurant = $this->getOwnRestaurant($id, $user);
        $body = get_body();

        $st = $this->db->prepare(
            'UPDATE restaurantes SET
                nome = COALESCE(?, nome),
                descricao = COALESCE(?, descricao),
                endereco = COALESCE(?, endereco),
                cidade = COALESCE(?, cidade),
                telefone = COALESCE(?, telefone),
                email = COALESCE(?, email),
                imagem = COALESCE(?, imagem),
                banner = COALESCE(?, banner),
                categoria = COALESCE(?, categoria),
                taxa_entrega = COALESCE(?, taxa_entrega),
                tempo_entrega = COALESCE(?, tempo_entrega),
                aberto = COALESCE(?, aberto)
             WHERE id = ?
             RETURNING *'
        );
        $st->execute([
            $body['nome']          ?? null,
            $body['descricao']     ?? null,
            $body['endereco']      ?? null,
            $body['cidade']        ?? null,
            $body['telefone']      ?? null,
            $body['email']         ?? null,
            $body['imagem']        ?? null,
            $body['banner']        ?? null,
            $body['categoria']     ?? null,
            $body['taxa_entrega']  ?? null,
            $body['tempo_entrega'] ?? null,
            isset($body['aberto']) ? ($body['aberto'] ? 'true' : 'false') : null,
            $id,
        ]);

        json_response($st->fetch());
    }

    // DELETE /restaurants/:id
    public function destroy(string $id, array $user): void
    {
        $this->getOwnRestaurant($id, $user);

        $st = $this->db->prepare('UPDATE restaurantes SET ativo = FALSE WHERE id = ?');
        $st->execute([$id]);

        json_response(['message' => 'Restaurante desativado.']);
    }

    private function getOwnRestaurant(string $id, array $user): array
    {
        $st = $this->db->prepare('SELECT * FROM restaurantes WHERE id = ?');
        $st->execute([$id]);
        $r = $st->fetch();

        if (!$r) json_response(['error' => 'Restaurante não encontrado.'], 404);

        if ($user['tipo'] !== 'admin' && $r['utilizador_id'] !== $user['id']) {
            json_response(['error' => 'Sem permissão.'], 403);
        }

        return $r;
    }
}
