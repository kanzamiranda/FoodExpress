<?php
// controllers/NotificationController.php
declare(strict_types=1);

class NotificationController
{
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    public function index(array $user): void
    {
        $st = $this->db->prepare(
            'SELECT * FROM notificacoes WHERE utilizador_id = ? ORDER BY criado_em DESC LIMIT 50'
        );
        $st->execute([$user['id']]);
        $list = $st->fetchAll();

        $unread = array_reduce($list, fn($c, $n) => $c + ($n['lida'] ? 0 : 1), 0);

        json_response(['data' => $list, 'unread' => $unread]);
    }

    public function readAll(array $user): void
    {
        $st = $this->db->prepare('UPDATE notificacoes SET lida = TRUE WHERE utilizador_id = ?');
        $st->execute([$user['id']]);
        json_response(['message' => 'Notificações marcadas como lidas.']);
    }
}
