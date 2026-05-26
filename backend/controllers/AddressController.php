<?php
// controllers/AddressController.php
declare(strict_types=1);

class AddressController
{
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    public function index(array $user): void
    {
        $st = $this->db->prepare('SELECT * FROM enderecos_entrega WHERE utilizador_id = ? ORDER BY principal DESC');
        $st->execute([$user['id']]);
        json_response($st->fetchAll());
    }

    public function store(array $user): void
    {
        $body = get_body();
        $rua  = trim($body['rua']    ?? '');
        $city = trim($body['cidade'] ?? '');

        if (!$rua || !$city) json_response(['error' => 'Rua e cidade são obrigatórios.'], 422);

        $st = $this->db->prepare(
            'INSERT INTO enderecos_entrega (utilizador_id, label, rua, numero, complemento, cidade, codigo_postal, principal)
             VALUES (?,?,?,?,?,?,?,?) RETURNING *'
        );
        $st->execute([
            $user['id'],
            $body['label']         ?? 'Casa',
            $rua,
            $body['numero']        ?? null,
            $body['complemento']   ?? null,
            $city,
            $body['codigo_postal'] ?? null,
            ($body['principal']    ?? false) ? 'true' : 'false',
        ]);

        json_response($st->fetch(), 201);
    }

    public function destroy(string $id, array $user): void
    {
        $st = $this->db->prepare('DELETE FROM enderecos_entrega WHERE id = ? AND utilizador_id = ? RETURNING id');
        $st->execute([$id, $user['id']]);
        if (!$st->fetch()) json_response(['error' => 'Endereço não encontrado.'], 404);
        json_response(['message' => 'Endereço removido.']);
    }
}
