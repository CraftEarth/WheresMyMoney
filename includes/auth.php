<?php

declare(strict_types=1);

function current_user_id(): ?int
{
    return isset($_SESSION['user_id'])
        ? (int) $_SESSION['user_id']
        : null;
}


function is_logged_in(): bool
{
    return current_user_id() !== null;
}


function require_login(): void
{
    if (is_logged_in()) {
        return;
    }

    header(
        'Location: ' . url('login.php')
    );

    exit;
}


function current_user(): ?array
{
    static $loaded = false;
    static $user = null;

    if ($loaded) {
        return $user;
    }

    $loaded = true;

    $userId = current_user_id();

    if ($userId === null) {
        return null;
    }

    $stmt = db()->prepare(
        '
        SELECT
            id,
            email,
            display_name,
            created_at
        FROM users
        WHERE id = :id
        LIMIT 1
        '
    );

    $stmt->execute([
        'id' => $userId,
    ]);

    $result = $stmt->fetch();

    $user = $result ?: null;

    return $user;
}


function csrf_token(): string
{
    if (
        !isset($_SESSION['csrf_token']) ||
        !is_string($_SESSION['csrf_token'])
    ) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}


function verify_csrf(
    ?string $token
): bool {
    return isset($_SESSION['csrf_token']) &&
        is_string($token) &&
        hash_equals(
            $_SESSION['csrf_token'],
            $token
        );
}
