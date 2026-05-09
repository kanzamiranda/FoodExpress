<?php
// ============================================================
// FoodExpress — Database Connection (PDO + Neon PostgreSQL)
// ============================================================
declare(strict_types=1);

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $host     = getenv('DB_HOST')     ?: 'ep-xxx.eu-central-1.aws.neon.tech';
    $port     = getenv('DB_PORT')     ?: '5432';
    $dbname   = getenv('DB_NAME')     ?: 'foodexpress';
    $user     = getenv('DB_USER')     ?: 'foodexpress_owner';
    $password = getenv('DB_PASSWORD') ?: '';
    $sslmode  = getenv('DB_SSLMODE')  ?: 'require';

    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $user, $password, $options);
    return $pdo;
}
