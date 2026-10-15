<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Dashboard';
$pageType = 'app';
$activePage = 'dashboard';

$userId = current_user_id();
$user = current_user();

$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');


$stmt = db()->prepare(
    '
    SELECT *
    FROM accounts
    WHERE user_id = :user_id
      AND account_status = "active"
    ORDER BY account_name ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$allAccounts = $stmt->fetchAll();

$totalCash = 0.00;
$availableCash = 0.00;
$netWorth = 0.00;

foreach ($allAccounts as $account) {
    if ((int) $account['include_in_cash'] === 1) {
        $totalCash += (float) $account[
            'balance_current'
        ];
    }

    if (
        (int) $account[
            'include_in_safe_to_spend'
        ] === 1
    ) {
        $availableCash += (float) $account[
            'balance_available'
        ];
    }

    if (
        (int) $account[
            'include_in_net_worth'
        ] === 1
    ) {
        $netWorth += (float) $account[
            'balance_current'
        ];
    }
}


/*
 * User financial preferences.
 */
$stmt = db()->prepare(
    '
    SELECT
        include_crypto_net_worth,
        include_crypto_safe_to_spend
    FROM user_settings
    WHERE user_id = :user_id
    LIMIT 1
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$financialSettings = $stmt->fetch() ?: [
    'include_crypto_net_worth' => 1,
    'include_crypto_safe_to_spend' => 0,
];


/*
 * Crypto portfolio value.
 */
$stmt = db()->prepare(
    '
    SELECT
        COALESCE(
            SUM(
                quantity * current_price
            ),
            0
        )
    FROM crypto_holdings
    WHERE user_id = :user_id
      AND is_archived = 0
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$cryptoValue = (float) $stmt->fetchColumn();


if (
    (int) $financialSettings[
        'include_crypto_net_worth'
    ] === 1
) {
    $netWorth += $cryptoValue;
}


if (
    (int) $financialSettings[
        'include_crypto_safe_to_spend'
    ] === 1
) {
    $availableCash += $cryptoValue;
}


$stmt = db()->prepare(
    '
    SELECT
        COALESCE(
            SUM(
                CASE
                    WHEN transaction_type = "income"
                    THEN amount
                    ELSE 0
                END
            ),
            0
        ) AS income,

        COALESCE(
            SUM(
                CASE
                    WHEN transaction_type = "expense"
                    THEN ABS(amount)
                    ELSE 0
                END
            ),
            0
        ) AS spending

    FROM transactions

    WHERE user_id = :user_id
      AND is_archived = 0
      AND transaction_status = "posted"
      AND transaction_date
          BETWEEN :month_start AND :month_end
    '
);

$stmt->execute([
    'user_id' => $userId,
    'month_start' => $monthStart,
    'month_end' => $monthEnd,
]);

$monthTotals = $stmt->fetch();

$monthlyIncome = (float) (
    $monthTotals['income'] ?? 0
);

$monthlySpending = (float) (
    $monthTotals['spending'] ?? 0
);


$stmt = db()->prepare(
    '
    SELECT
        COALESCE(
            SUM(expected_amount),
            0
        )
    FROM bills
    WHERE user_id = :user_id
      AND is_archived = 0
      AND bill_status IN (
          "upcoming",
          "due_soon",
          "overdue"
      )
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$upcomingBills = (float) $stmt->fetchColumn();

$safeToSpend = max(
    0,
    $availableCash - $upcomingBills
);


$stmt = db()->prepare(
    '
    SELECT *
    FROM bills
    WHERE user_id = :user_id
      AND is_archived = 0
      AND bill_status IN (
          "upcoming",
          "due_soon",
          "overdue"
      )
    ORDER BY
        next_due_date ASC,
        id ASC
    LIMIT 4
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$dashboardBills = $stmt->fetchAll();


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
    LIMIT 6
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$recentTransactions = $stmt->fetchAll();


$stmt = db()->prepare(
    '
    SELECT
        category,
        SUM(ABS(amount)) AS amount
    FROM transactions
    WHERE user_id = :user_id
      AND is_archived = 0
      AND transaction_status = "posted"
      AND transaction_type = "expense"
      AND transaction_date
          BETWEEN :month_start AND :month_end
    GROUP BY category
    ORDER BY amount DESC
    LIMIT 6
    '
);

$stmt->execute([
    'user_id' => $userId,
    'month_start' => $monthStart,
    'month_end' => $monthEnd,
]);

$categoryRows = $stmt->fetchAll();

$spending = [];

foreach ($categoryRows as $row) {
    $categoryAmount = (float) $row['amount'];

    $percent = $monthlySpending > 0
        ? round(
            ($categoryAmount / $monthlySpending)
            * 100
        )
        : 0;

    $spending[] = [
        'name' => $row['category'],
        'amount' => $categoryAmount,
        'percent' => $percent,
    ];
}


$habitCategories = [
    'Cannabis',
    'Alcohol',
];

$habitSpending = [];

foreach ($habitCategories as $habitCategory) {
    $habitSpending[$habitCategory] = 0.00;
}

foreach ($spending as $item) {
    if (
        array_key_exists(
            $item['name'],
            $habitSpending
        )
    ) {
        $habitSpending[
            $item['name']
        ] = (float) $item['amount'];
    }
}


$summary = [
    'cash' => $totalCash,
    'income' => $monthlyIncome,
    'spent' => $monthlySpending,
    'bills' => $upcomingBills,
    'safe_to_spend' => $safeToSpend,
    'net_worth' => $netWorth,
    'crypto' => $cryptoValue,
];

$accounts = array_slice(
    $allAccounts,
    0,
    5
);

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/dashboard.php';
require_once APP_ROOT . '/includes/footer.php';



