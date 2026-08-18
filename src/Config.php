<?php
declare(strict_types=1);

final class Config
{
    public static function database(): array
    {
        $port = getenv('IAMROOT_DB_PORT');
        $charset = getenv('IAMROOT_DB_CHARSET');

        return [
            'host' => self::required('IAMROOT_DB_HOST'),
            'port' => $port !== false && $port !== '' ? (int)$port : 3306,
            'name' => self::required('IAMROOT_DB_NAME'),
            'user' => self::required('IAMROOT_DB_USER'),
            'pass' => self::required('IAMROOT_DB_PASS'),
            'charset' => $charset !== false && $charset !== '' ? $charset : 'utf8mb4',
        ];
    }

    private static function required(string $name): string
    {
        $value = getenv($name);
        if ($value === false || trim($value) === '') {
            throw new RuntimeException("Variable d'environnement manquante : {$name}");
        }
        return $value;
    }
}
