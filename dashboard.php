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


/*
 * BEGIN DASHBOARD CRYPTO ROLLUP
 *
 * Dashboard crypto value =
 * manual priced holdings
 * +
 * synced priced wallet assets.
 *
 * Unpriced wallet tokens are intentionally excluded.
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
      AND quantity > 0
      AND current_price > 0
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$manualCryptoValue =
    (float) $stmt->fetchColumn();


$stmt = db()->prepare(
    '
    SELECT
        COALESCE(
            SUM(current_value),
            0
        )
    FROM crypto_assets
    WHERE user_id = :user_id
      AND is_archived = 0
      AND quantity > 0
      AND current_price > 0
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$walletCryptoValue =
    (float) $stmt->fetchColumn();


$cryptoValue =
    $manualCryptoValue
    + $walletCryptoValue;

/* END DASHBOARD CRYPTO ROLLUP */

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


/*
 * Current month income and NET spending.
 *
 * Refunds reduce spending.
 * Transfers are ignored automatically because
 * they are neither income nor expense/refund.
 */
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

                    WHEN transaction_type = "refund"
                    THEN -ABS(amount)

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


/*
 * Bills.
 */
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


/*
 * Recent activity.
 */
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


/*
 * Net spending by category.
 *
 * A refund reduces its original category.
 * Categories that are net credits are omitted from
 * "Where Did My Money Go?" because they are not
 * money that went out this month.
 */
$stmt = db()->prepare(
    '
    SELECT
        category,

        SUM(
            CASE
                WHEN transaction_type = "expense"
                THEN ABS(amount)

                WHEN transaction_type = "refund"
                THEN -ABS(amount)

                ELSE 0
            END
        ) AS amount

    FROM transactions

    WHERE user_id = :user_id
      AND is_archived = 0
      AND transaction_status = "posted"
      AND transaction_type IN (
          "expense",
          "refund"
      )
      AND transaction_date
          BETWEEN :month_start AND :month_end

    GROUP BY category

    HAVING amount > 0

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

$breakdownTotal = 0.00;

foreach ($categoryRows as $row) {
    $breakdownTotal += (float) $row['amount'];
}

$spending = [];

foreach ($categoryRows as $row) {
    $categoryAmount = (float) $row['amount'];

    $percent = $breakdownTotal > 0
        ? round(
            ($categoryAmount / $breakdownTotal)
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


/*
 * BEGIN DASHBOARD ALLOCATION FIX
 */

$cryptoPercentOfNetWorth = 0.00;
$cashPercentOfNetWorth = 0.00;

if ($netWorth > 0) {

    $cryptoPercentOfNetWorth =
        (
            $cryptoValue
            / $netWorth
        ) * 100;

    $cashPercentOfNetWorth =
        (
            $totalCash
            / $netWorth
        ) * 100;
}

/* END DASHBOARD ALLOCATION FIX */

/*
 * BEGIN DASHBOARD DEBT TOTAL
 *
 * Liabilities are active accounts included in net worth
 * whose current balance is negative.
 */

$totalDebt = 0.00;

foreach ($allAccounts as $account) {

    $balance =
        (float) (
            $account['balance_current']
            ?? 0
        );

    if (
        (int) (
            $account['include_in_net_worth']
            ?? 0
        ) === 1 &&
        $balance < 0
    ) {
        $totalDebt += abs($balance);
    }
}

/* END DASHBOARD DEBT TOTAL */

$summary = [
    'cash' => $totalCash,
    'income' => $monthlyIncome,
    'spent' => $monthlySpending,
    'bills' => $upcomingBills,
    'safe_to_spend' => $safeToSpend,
    'net_worth' => $netWorth,
    'crypto' => $cryptoValue,
];

/*
 * BEGIN DASHBOARD CRYPTO SUMMARY KEYS
 */

$summary['crypto'] =
    $cryptoValue;

$summary['crypto_percent'] =
    $cryptoPercentOfNetWorth;

$summary['cash_percent'] =
    $cashPercentOfNetWorth;

/* END DASHBOARD CRYPTO SUMMARY KEYS */

/*
 * BEGIN MONEY ALLOCATION SUMMARY
 */

$summary['cash'] =
    $totalCash;

$summary['crypto'] =
    $cryptoValue;

$summary['debt'] =
    $totalDebt;

$summary['bills'] =
    $upcomingBills;

$summary['net_worth'] =
    $netWorth;

$summary['cash_percent'] =
    $netWorth > 0
        ? ($totalCash / $netWorth) * 100
        : 0.00;

$summary['crypto_percent'] =
    $netWorth > 0
        ? ($cryptoValue / $netWorth) * 100
        : 0.00;

/* END MONEY ALLOCATION SUMMARY */

$accounts = array_slice(
    $allAccounts,
    0,
    5
);

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/dashboard.php';
require_once APP_ROOT . '/includes/footer.php';
