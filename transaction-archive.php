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

$transactionId = (int) (
    $_POST['transaction_id'] ?? 0
);

$pdo = db();

$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare(
        '
        SELECT
            account_id,
            amount,
            transaction_status
        FROM transactions
        WHERE id = :id
          AND user_id = :user_id
          AND is_archived = 0
        LIMIT 1
        '
    );

    $stmt->execute([
        'id' => $transactionId,
        'user_id' => $userId,
    ]);

    $transaction = $stmt->fetch();

    if ($transaction) {
        $effect = transaction_balance_effect(
            (float) $transaction['amount'],
            (string) $transaction[
                'transaction_status'
            ]
        );

        adjust_manual_account_balance(
            $pdo,
            $userId,
            (int) $transaction['account_id'],
            -$effect
        );

        $stmt = $pdo->prepare(
            '
            UPDATE transactions
            SET is_archived = 1
            WHERE id = :id
              AND user_id = :user_id
            '
        );

        $stmt->execute([
            'id' => $transactionId,
            'user_id' => $userId,
        ]);
    }

    $pdo->commit();
} catch (Throwable $exception) {
    $pdo->rollBack();

    throw $exception;
}

header(
    'Location: ' . url('transactions.php')
);

exit;
