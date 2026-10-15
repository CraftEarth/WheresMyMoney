<?php

declare(strict_types=1);

function transaction_balance_effect(
    float $amount,
    string $status
): float {
    if ($status !== 'posted') {
        return 0.00;
    }

    return $amount;
}


function adjust_manual_account_balance(
    PDO $pdo,
    int $userId,
    int $accountId,
    float $effect
): void {
    if (abs($effect) < 0.00001) {
        return;
    }

    $stmt = $pdo->prepare(
        '
        UPDATE accounts
        SET
            balance_current =
                balance_current + :effect_current,

            balance_available =
                balance_available + :effect_available

        WHERE id = :account_id
          AND user_id = :user_id
          AND source_type = "manual"
          AND account_status = "active"
        '
    );

    $stmt->execute([
        'effect_current' => $effect,
        'effect_available' => $effect,
        'account_id' => $accountId,
        'user_id' => $userId,
    ]);
}
