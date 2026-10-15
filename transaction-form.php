<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$userId = current_user_id();

$transactionId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$transaction = null;
$errorMessage = null;

$stmt = db()->prepare(
    '
    SELECT
        id,
        account_name
    FROM accounts
    WHERE user_id = :user_id
      AND account_status = "active"
    ORDER BY account_name ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$accounts = $stmt->fetchAll();

if ($accounts === []) {
    header(
        'Location: ' . url('accounts.php')
    );

    exit;
}

if ($transactionId > 0) {
    $stmt = db()->prepare(
        '
        SELECT *
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

    if (!$transaction) {
        http_response_code(404);
        exit('Transaction not found.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !verify_csrf(
            $_POST['csrf_token'] ?? null
        )
    ) {
        $errorMessage =
            'The form expired. Please try again.';
    } else {
        $accountId = (int) (
            $_POST['account_id'] ?? 0
        );

        $date = trim(
            (string) ($_POST['transaction_date'] ?? '')
        );

        $description = trim(
            (string) ($_POST['description'] ?? '')
        );

        $category = trim(
            (string) ($_POST['category'] ?? '')
        );

        $type = trim(
            (string) ($_POST['transaction_type'] ?? '')
        );

        $status = trim(
            (string) ($_POST['transaction_status'] ?? 'posted')
        );

        $enteredAmount = abs(
            (float) ($_POST['amount'] ?? 0)
        );

        $notes = trim(
            (string) ($_POST['notes'] ?? '')
        );

        $isRecurring = isset(
            $_POST['is_recurring']
        ) ? 1 : 0;

        $saveRule = isset(
            $_POST['save_rule']
        );

        $ruleMatchValue = trim(
            (string) (
                $_POST['rule_match_value']
                ?? ''
            )
        );

        $isTransfer = in_array(
            $type,
            [
                'transfer_out',
                'transfer_in',
            ],
            true
        ) ? 1 : 0;

        $validTypes = [
            'income',
            'expense',
            'transfer_out',
            'transfer_in',
        ];

        if (
            !in_array(
                $type,
                $validTypes,
                true
            )
        ) {
            $errorMessage =
                'Choose a valid transaction type.';
        }

        $stmt = db()->prepare(
            '
            SELECT id
            FROM accounts
            WHERE id = :id
              AND user_id = :user_id
              AND account_status = "active"
            LIMIT 1
            '
        );

        $stmt->execute([
            'id' => $accountId,
            'user_id' => $userId,
        ]);

        if (!$stmt->fetch()) {
            $errorMessage =
                'Choose a valid account.';
        }

        if ($date === '') {
            $errorMessage =
                'Transaction date is required.';
        }

        if ($description === '') {
            $errorMessage =
                'Description is required.';
        }

        if ($category === '') {
            $category = 'Uncategorized';
        }

        if ($enteredAmount <= 0) {
            $errorMessage =
                'Amount must be greater than zero.';
        }

        $amount = match ($type) {
            'income',
            'transfer_in' => $enteredAmount,

            'expense',
            'transfer_out' => -$enteredAmount,

            default => 0.00,
        };

        if ($errorMessage === null) {
            $pdo = db();

            $pdo->beginTransaction();

            try {
                if ($transactionId > 0) {
                    $oldEffect =
                        transaction_balance_effect(
                            (float) $transaction['amount'],
                            (string) $transaction[
                                'transaction_status'
                            ]
                        );

                    adjust_manual_account_balance(
                        $pdo,
                        $userId,
                        (int) $transaction['account_id'],
                        -$oldEffect
                    );

                    $stmt = $pdo->prepare(
                        '
                        UPDATE transactions
                        SET
                            account_id = :account_id,
                            transaction_date = :transaction_date,
                            description = :description,
                            category = :category,
                            transaction_type = :transaction_type,
                            transaction_status = :transaction_status,
                            amount = :amount,
                            is_transfer = :is_transfer,
                            is_recurring = :is_recurring,
                            needs_review = 0,
                            notes = :notes
                        WHERE id = :id
                          AND user_id = :user_id
                        '
                    );

                    $stmt->execute([
                        'account_id' => $accountId,
                        'transaction_date' => $date,
                        'description' => $description,
                        'category' => $category,
                        'transaction_type' => $type,
                        'transaction_status' => $status,
                        'amount' => $amount,
                        'is_transfer' => $isTransfer,
                        'is_recurring' => $isRecurring,
                        'notes' => $notes !== ''
                            ? $notes
                            : null,
                        'id' => $transactionId,
                        'user_id' => $userId,
                    ]);
                } else {
                    $stmt = $pdo->prepare(
                        '
                        INSERT INTO transactions (
                            user_id,
                            account_id,
                            transaction_date,
                            description,
                            raw_description,
                            category,
                            transaction_type,
                            transaction_status,
                            amount,
                            is_transfer,
                            is_recurring,
                            needs_review,
                            notes
                        )
                        VALUES (
                            :user_id,
                            :account_id,
                            :transaction_date,
                            :description,
                            :description,
                            :category,
                            :transaction_type,
                            :transaction_status,
                            :amount,
                            :is_transfer,
                            :is_recurring,
                            0,
                            :notes
                        )
                        '
                    );

                    $stmt->execute([
                        'user_id' => $userId,
                        'account_id' => $accountId,
                        'transaction_date' => $date,
                        'description' => $description,
                        'category' => $category,
                        'transaction_type' => $type,
                        'transaction_status' => $status,
                        'amount' => $amount,
                        'is_transfer' => $isTransfer,
                        'is_recurring' => $isRecurring,
                        'notes' => $notes !== ''
                            ? $notes
                            : null,
                    ]);
                }

                $newEffect =
                    transaction_balance_effect(
                        $amount,
                        $status
                    );

                adjust_manual_account_balance(
                    $pdo,
                    $userId,
                    $accountId,
                    $newEffect
                );

                if (
                    $saveRule &&
                    $ruleMatchValue !== ''
                ) {
                    $stmt = $pdo->prepare(
                        '
                        INSERT INTO transaction_rules (
                            user_id,
                            match_value,
                            display_name,
                            category,
                            priority,
                            enabled
                        )
                        VALUES (
                            :user_id,
                            :match_value,
                            :display_name,
                            :category,
                            100,
                            1
                        )
                        '
                    );

                    $stmt->execute([
                        'user_id' => $userId,
                        'match_value' => $ruleMatchValue,
                        'display_name' => $description,
                        'category' => $category,
                    ]);
                }

                $pdo->commit();

                header(
                    'Location: ' . url('transactions.php')
                );

                exit;
            } catch (Throwable $exception) {
                $pdo->rollBack();

                $errorMessage =
                    'Transaction could not be saved.';
            }
        }
    }
}

$pageTitle = $transactionId > 0
    ? 'Edit Transaction'
    : 'Add Transaction';

$pageType = 'app';
$activePage = 'transactions';

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/transaction-form.php';
require_once APP_ROOT . '/includes/footer.php';
