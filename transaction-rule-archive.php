<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header(
        'Location: ' . url('transaction-rules.php')
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

$stmt = db()->prepare(
    '
    UPDATE transaction_rules
    SET is_archived = 1
    WHERE id = :id
      AND user_id = :user_id
    '
);

$stmt->execute([
    'id' => (int) ($_POST['rule_id'] ?? 0),
    'user_id' => current_user_id(),
]);

header(
    'Location: ' . url('transaction-rules.php')
);

exit;
