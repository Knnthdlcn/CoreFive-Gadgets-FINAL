<?php

/**
 * Creates a MySQL database for running automated tests.
 *
 * Usage:
 *   php scripts/create_test_database.php [database_name]
 *
 * Reads credentials from the project's .env file.
 */

$projectRoot = dirname(__DIR__);
$envPath = $projectRoot . DIRECTORY_SEPARATOR . '.env';

if (!file_exists($envPath)) {
    fwrite(STDERR, "Missing .env at {$envPath}. Create it first (copy .env.example .env).\n");
    exit(1);
}

$dbName = $argv[1] ?? 'eshop_test';

$env = file_get_contents($envPath);

$readEnv = static function (string $key) use ($env): ?string {
    if (!preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $env, $m)) {
        return null;
    }
    $value = trim($m[1]);
    if ($value === '') {
        return '';
    }
    if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
        $value = substr($value, 1, -1);
    }
    return $value;
};

$host = $readEnv('DB_HOST') ?? '127.0.0.1';
$port = $readEnv('DB_PORT') ?? '3306';
$user = $readEnv('DB_USERNAME') ?? 'root';
$pass = $readEnv('DB_PASSWORD') ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );

    // Allow only simple DB names to avoid injection.
    if (!preg_match('/^[A-Za-z0-9_]+$/', $dbName)) {
        throw new RuntimeException('Invalid database name. Use only letters, numbers, and underscores.');
    }

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    fwrite(STDOUT, "OK: ensured database exists: {$dbName}\n");
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: {$e->getMessage()}\n");
    exit(1);
}
