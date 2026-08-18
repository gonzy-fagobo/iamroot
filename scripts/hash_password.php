<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Ce script doit être exécuté en CLI.\n");
    exit(1);
}

$password = $argv[1] ?? '';
if ($password === '') {
    fwrite(STDERR, "Usage : php scripts/hash_password.php 'mot-de-passe'\n");
    exit(2);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
if ($hash === false) {
    fwrite(STDERR, "Impossible de générer le hash.\n");
    exit(3);
}

fwrite(STDOUT, $hash . PHP_EOL);
