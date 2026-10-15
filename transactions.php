<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Transactions';
$pageType = 'app';
$activePage = 'transactions';

$userId = current_user_id();

$stmt = db()->prepare(
    '
    SELECT
        t.*,
        a.account_name
    FROM transactions t
    INNER JOIN accounts a
        ON a.id = t.account_id
       AND a.user_id = t.user_id
    WHERE t.user_id = :user_id
      AND t.is_archived = 0
    ORDER BY
        t.transaction_date DESC,
        t.id DESC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$transactions = $stmt->fetchAll();

$totalIncome = 0.00;
$totalExpenses = 0.00;
$totalTransfers = 0.00;

foreach ($transactions as $transaction) {
    $amount = (float) $transaction['amount'];

    if (
        $transaction['transaction_type']
        === 'income'
    ) {
        $totalIncome += $amount;
    }

    if (
        $transaction['transaction_type']
        === 'expense'
    ) {
        $totalExpenses += abs($amount);
    }

    if ((int) $transaction['is_transfer'] === 1) {
        $totalTransfers += abs($amount);
    }
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/transactions.php';
require_once APP_ROOT . '/includes/footer.php';
