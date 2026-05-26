<?php
// controllers/AdminController.php

declare(strict_types=1);

class AdminController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    private function requireAdmin(array $user): void
    {
        AuthMiddleware::requireRole($user, 'admin');
    }

    // GET /admin/users
    public function users(array $user): void
    {
        $this->requireAdmin($user);

        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = min(100, max(1, (int)($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        $tipo   = $_GET['tipo']   ?? null;
        $search = trim($_GET['search'] ?? '');

        $where  = ['1=1'];
        $params = [];

        if ($tipo) { $where[] = 'tipo = ?'; $params[] = $tipo; }
        if ($search) {
            $where[] = '(nome ILIKE ? OR email ILIKE ?)';
            $params[] = "%{$search}%"; $params[] = "%{$search}%";
        }

        $w = implode(' AND ', $where);

        $count = $this->db->prepare("SELECT COUNT(*) FROM utilizadores WHERE {$w}");
        $count->execute($params);
        $total = (int)$count->fetchColumn();

        $st = $this->db->prepare(
            "SELECT id, nome, email, telefone, tipo, ativo, criado_em FROM utilizadores
             WHERE {$w} ORDER BY criado_em DESC LIMIT {$limit} OFFSET {$offset}"
        );
        $st->execute($params);

        json_response([
            'data'       => $st->fetchAll(),
            'total'      => $total,
            'page'       => $page,
            'totalPages' => (int)ceil($total / $limit),
        ]);
    }

    // PUT /admin/users/:id
    public function updateUser(string $id, array $user): void
    {
        $this->requireAdmin($user);

        $body = get_body();
        $st   = $this->db->prepare(
            'UPDATE utilizadores SET
                ativo = COALESCE(?, ativo),
                tipo  = COALESCE(?, tipo)
             WHERE id = ?
             RETURNING id, nome, email, tipo, ativo'
        );
        $st->execute([
            isset($body['ativo']) ? ($body['ativo'] ? 'true' : 'false') : null,
            $body['tipo'] ?? null,
            $id,
        ]);
        $updated = $st->fetch();
        if (!$updated) json_response(['error' => 'Utilizador não encontrado.'], 404);

        json_response($updated);
    }

    // GET /admin/stats
    public function stats(array $user): void
    {
        $this->requireAdmin($user);

        $stats = [];

        // Totais gerais
        foreach ([
            'total_users'       => "SELECT COUNT(*) FROM utilizadores WHERE tipo='cliente'",
            'total_restaurants' => "SELECT COUNT(*) FROM restaurantes WHERE ativo=TRUE",
            'total_orders'      => "SELECT COUNT(*) FROM pedidos",
            'total_revenue'     => "SELECT COALESCE(SUM(total),0) FROM pedidos WHERE status='entregue'",
        ] as $key => $sql) {
            $st = $this->db->query($sql);
            $stats[$key] = $st->fetchColumn();
        }

        // Pedidos por status
        $st = $this->db->query(
            "SELECT status, COUNT(*) AS total FROM pedidos GROUP BY status"
        );
        $stats['orders_by_status'] = $st->fetchAll();

        // Receita por dia (últimos 30 dias)
        $st = $this->db->query(
            "SELECT DATE(criado_em) AS dia, SUM(total) AS receita, COUNT(*) AS pedidos
             FROM pedidos
             WHERE status = 'entregue'
               AND criado_em >= NOW() - INTERVAL '30 days'
             GROUP BY DATE(criado_em)
             ORDER BY dia"
        );
        $stats['revenue_by_day'] = $st->fetchAll();

        // Top restaurantes
        $st = $this->db->query(
            "SELECT r.nome, COUNT(p.id) AS total_pedidos, SUM(p.total) AS receita
             FROM pedidos p
             JOIN restaurantes r ON r.id = p.restaurante_id
             WHERE p.status = 'entregue'
             GROUP BY r.id, r.nome
             ORDER BY receita DESC
             LIMIT 5"
        );
        $stats['top_restaurants'] = $st->fetchAll();

        // Novos utilizadores por mês (últimos 6 meses)
        $st = $this->db->query(
            "SELECT TO_CHAR(criado_em, 'YYYY-MM') AS mes, COUNT(*) AS total
             FROM utilizadores
             WHERE criado_em >= NOW() - INTERVAL '6 months'
             GROUP BY mes ORDER BY mes"
        );
        $stats['users_by_month'] = $st->fetchAll();

        json_response($stats);
    }

    // GET /admin/reports?type=orders|sales&format=json|csv
    public function reports(array $user): void
    {
        $this->requireAdmin($user);

        $type   = $_GET['type']   ?? 'orders';
        $format = $_GET['format'] ?? 'json';
        $from   = $_GET['from']   ?? date('Y-m-01');
        $to     = $_GET['to']     ?? date('Y-m-d');

        if ($type === 'orders') {
            $st = $this->db->prepare(
                "SELECT p.id, u.nome AS cliente, r.nome AS restaurante,
                        p.status, p.total, p.taxa_entrega, p.metodo_pagamento,
                        p.criado_em
                 FROM pedidos p
                 JOIN utilizadores u ON u.id = p.utilizador_id
                 JOIN restaurantes r ON r.id = p.restaurante_id
                 WHERE p.criado_em BETWEEN ? AND ?::date + 1
                 ORDER BY p.criado_em DESC"
            );
            $st->execute([$from, $to]);
        } else {
            // sales report
            $st = $this->db->prepare(
                "SELECT DATE(p.criado_em) AS dia,
                        COUNT(*) AS pedidos, SUM(p.total) AS receita_bruta,
                        SUM(p.taxa_entrega) AS taxas, COUNT(DISTINCT p.utilizador_id) AS clientes_unicos
                 FROM pedidos p
                 WHERE p.status = 'entregue'
                   AND p.criado_em BETWEEN ? AND ?::date + 1
                 GROUP BY DATE(p.criado_em)
                 ORDER BY dia"
            );
            $st->execute([$from, $to]);
        }

        $rows = $st->fetchAll();

        if ($format === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"report_{$type}_{$from}_{$to}.csv\"");

            if (!empty($rows)) {
                $out = fopen('php://output', 'w');
                fputcsv($out, array_keys($rows[0]));
                foreach ($rows as $row) fputcsv($out, $row);
                fclose($out);
            }
            exit;
        }

        json_response(['data' => $rows, 'from' => $from, 'to' => $to]);
    }
}
