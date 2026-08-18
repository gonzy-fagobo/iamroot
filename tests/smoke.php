<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Config.php';

function ok(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "[FAIL] {$message}\n");
        exit(1);
    }
    fwrite(STDOUT, "[OK] {$message}\n");
}

$password = 'MotDePasse-Test-2026!';
$hash = password_hash($password, PASSWORD_DEFAULT);
ok(is_string($hash) && $hash !== '', 'password_hash');
ok(password_verify($password, $hash), 'password_verify valide');
ok(!password_verify('incorrect', $hash), 'password_verify refuse');

putenv('IAMROOT_DB_HOST=127.0.0.1');
putenv('IAMROOT_DB_PORT=3307');
putenv('IAMROOT_DB_NAME=iamroot_test');
putenv('IAMROOT_DB_USER=test_user');
putenv('IAMROOT_DB_PASS=test_password');
putenv('IAMROOT_DB_CHARSET=utf8mb4');

$config = Config::database();
ok($config['host'] === '127.0.0.1', 'Config hôte');
ok($config['port'] === 3307, 'Config port');
ok($config['name'] === 'iamroot_test', 'Config base');
ok($config['charset'] === 'utf8mb4', 'Config charset');

fwrite(STDOUT, "Smoke tests terminés.\n");
