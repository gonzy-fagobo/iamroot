<?php
declare(strict_types=1);

final class AuthService
{
    public function __construct(private PDO $pdo) {}

    public function authenticate(string $username, string $password, int $applicationId): array
    {
        $sql = <<<'SQL'
SELECT
    u.id AS user_id, u.username, u.password_hash, u.email,
    u.is_active AS user_active,
    a.id AS app_id, a.code AS app_code, a.nombre AS app_name,
    a.is_active AS app_active,
    ua.is_active AS access_active,
    r.id AS role_id, r.nombre AS role_name
FROM usuarios u
INNER JOIN usuario_aplicaciones ua ON ua.id_user = u.id
INNER JOIN aplicaciones a ON a.id = ua.id_app
INNER JOIN roles r ON r.id = ua.id_rol
WHERE u.username = :username
  AND a.id = :application_id
LIMIT 1
SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'application_id' => $applicationId,
        ]);
        $row = $stmt->fetch();

        if (
            !$row
            || !password_verify($password, (string)$row['password_hash'])
            || (int)$row['user_active'] !== 1
            || (int)$row['app_active'] !== 1
            || (int)$row['access_active'] !== 1
        ) {
            throw new DomainException('INVALID_CREDENTIALS');
        }

        return [
            'user' => [
                'id' => (int)$row['user_id'],
                'username' => (string)$row['username'],
                'email' => (string)$row['email'],
                'role' => (string)$row['role_name'],
            ],
            'application' => [
                'id' => (int)$row['app_id'],
                'code' => (string)$row['app_code'],
                'name' => (string)$row['app_name'],
            ],
            'permissions' => $this->permissionsForRole((int)$row['role_id']),
        ];
    }

    private function permissionsForRole(int $roleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.code
             FROM permisos p
             INNER JOIN rol_permisos rp ON rp.id_permiso = p.id
             WHERE rp.id_rol = :role_id
             ORDER BY p.code'
        );
        $stmt->execute(['role_id' => $roleId]);

        return array_map(
            static fn(array $row): string => (string)$row['code'],
            $stmt->fetchAll()
        );
    }
}
