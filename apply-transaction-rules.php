<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header(
        'Location: ' . url('transactions.php')
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
$pdo = db();

$stmt = $pdo->prepare(
    '
    SELECT
        id,
        raw_description,
        description,
        category
    FROM transactions
    WHERE user_id = :user_id
      AND is_archived = 0
    ORDER BY id ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$transactions = $stmt->fetchAll();

$updateStmt = $pdo->prepare(
    '
    UPDATE transactions
    SET
        description = :description,
        category = :category
    WHERE id = :id
      AND user_id = :user_id
    '
);

$updated = 0;

$pdo->beginTransaction();

try {
    foreach ($transactions as $transaction) {
        $rawDescription = trim(
            (string) (
                $transaction[
                    'raw_description'
                ]
                ?: $transaction[
                    'description'
                ]
            )
        );

        $normalized =
            normalize_transaction(
                $pdo,
                $userId,
                $rawDescription,
                (string) $transaction[
                    'description'
                ],
                (string) $transaction[
                    'category'
                ]
            );

        if (!$normalized['matched']) {
            continue;
        }

        $updateStmt->execute([
            'description' =>
                $normalized[
                    'description'
                ],

            'category' =>
                $normalized[
                    'category'
                ],

            'id' =>
                (int) $transaction['id'],

            'user_id' =>
                $userId,
        ]);

        $updated++;
    }

    $pdo->commit();

    $_SESSION[
        'rules_applied_message'
    ] =
        $updated
        . ' transaction(s) updated.';
} catch (Throwable $exception) {
    $pdo->rollBack();

    $_SESSION[
        'rules_applied_message'
    ] =
        'Rules could not be applied.';
}

header(
    'Location: ' . url('transactions.php')
);

exit;
