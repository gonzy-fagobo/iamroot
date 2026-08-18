<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Config.php';
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/JsonResponse.php';
require_once __DIR__ . '/../../src/AuthService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    JsonResponse::send([
        'success' => false,
        'code' => 'METHOD_NOT_ALLOWED',
        'message' => 'Méthode non autorisée.',
    ], 405);
}

try {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw !== false ? $raw : '', true, 32, JSON_THROW_ON_ERROR);

    if (!is_array($input)) {
        throw new JsonException('Objet JSON attendu.');
    }

    $username = trim((string)($input['username'] ?? ''));
    $password = (string)($input['password'] ?? '');
    $applicationId = filter_var(
        $input['application_id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($username === '' || $password === '' || $applicationId === false) {
        JsonResponse::send(['success' => false, 'code' => 'INVALID_REQUEST', 'message' => 'Requête invalide.'], 400);
    }

    if (mb_strlen($username, 'UTF-8') > 80 || strlen($password) > 4096) {
        JsonResponse::send(['success' => false, 'code' => 'INVALID_REQUEST', 'message' => 'Requête invalide.'], 400);
    }

    $service = new AuthService(Database::connect(Config::database()));
    $result = $service->authenticate($username, $password, (int)$applicationId);

    JsonResponse::send([
        'success' => true,
        'code' => 'LOGIN_OK',
        'user' => $result['user'],
        'application' => $result['application'],
        'permissions' => $result['permissions'],
    ]);
} catch (DomainException $e) {
    JsonResponse::send([
        'success' => false,
        'code' => 'INVALID_CREDENTIALS',
        'message' => 'Identifiants ou autorisation invalides.',
    ], 401);
} catch (JsonException $e) {
    JsonResponse::send(['success' => false, 'code' => 'INVALID_JSON', 'message' => 'JSON invalide.'], 400);
} catch (Throwable $e) {
    error_log('[IamRoot] ' . $e->getMessage());
    JsonResponse::send(['success' => false, 'code' => 'INTERNAL_ERROR', 'message' => 'Erreur interne.'], 500);
}
