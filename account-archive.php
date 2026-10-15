<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header(
        'Location: ' . url('accounts.php')
    );

    exit;
}

if (
    !verify_csrf(
        $_POST['csrf_token'] ?? null
    )
) {
    exit('Invalid request.');
}

$userId = current_user_id();
$accountId = (int) (
    $_POST['account_id'] ?? 0
);

$stmt = db()->prepare(
    '
    UPDATE accounts
    SET
        account_status = "archived"
    WHERE id = :id
      AND user_id = :user_id
    '
);

$stmt->execute([
    'id' => $accountId,
    'user_id' => $userId,
]);

header(
    'Location: ' . url('accounts.php')
);

exit;
