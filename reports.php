<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Reports';
$pageType = 'app';
$activePage = 'reports';

$userId = current_user_id();

$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t');


/*
 * Current month income / net spending.
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
    'month_start' => $currentMonthStart,
    'month_end' => $currentMonthEnd,
]);

$currentTotals = $stmt->fetch();

$currentIncome = (float) (
    $currentTotals['income'] ?? 0
);

$currentSpending = (float) (
    $currentTotals['spending'] ?? 0
);

$netCashFlow =
    $currentIncome - $currentSpending;

$rawSavingsRate =
    $currentIncome > 0
        ? ($netCashFlow / $currentIncome) * 100
        : 0;

$savingsRate = min(
    100,
    $rawSavingsRate
);


/*
 * Current unpaid bills.
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


/*
 * Current month budget totals.
 */
$stmt = db()->prepare(
    '
    SELECT
        COALESCE(
            SUM(monthly_limit),
            0
        )
    FROM budgets
    WHERE user_id = :user_id
      AND is_archived = 0
      AND budget_month = :budget_month
    '
);

$stmt->execute([
    'user_id' => $userId,
    'budget_month' => $currentMonthStart,
]);

$totalBudget = (float) $stmt->fetchColumn();


/*
 * Current month net spending categories.
 *
 * Refunds offset spending in the same category.
 * Net-credit categories are not shown as spending.
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
    '
);

$stmt->execute([
    'user_id' => $userId,
    'month_start' => $currentMonthStart,
    'month_end' => $currentMonthEnd,
]);

$categories = $stmt->fetchAll();

$breakdownTotal = 0.00;

foreach ($categories as $category) {
    $breakdownTotal += (float) $category['amount'];
}

foreach ($categories as &$category) {
    $category['percent'] =
        $breakdownTotal > 0
            ? (
                (float) $category['amount']
                / $breakdownTotal
            ) * 100
            : 0;
}

unset($category);


/*
 * Last 6 calendar months.
 */
$monthlyHistory = [];

for ($i = 0; $i < 6; $i++) {
    $monthStart = date(
        'Y-m-01',
        strtotime("-{$i} months")
    );

    $monthEnd = date(
        'Y-m-t',
        strtotime($monthStart)
    );

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

    $row = $stmt->fetch();

    $income = (float) (
        $row['income'] ?? 0
    );

    $spending = (float) (
        $row['spending'] ?? 0
    );

    $monthlyHistory[] = [
        'month' => date(
            'F Y',
            strtotime($monthStart)
        ),
        'income' => $income,
        'spending' => $spending,
        'net' => $income - $spending,
    ];
}


/*
 * Largest positive spending category.
 */
$largestCategory = $categories[0] ?? null;


/*
 * Habit spending.
 */
$habitSpending = [
    'Cannabis' => 0.00,
    'Alcohol' => 0.00,
];

foreach ($categories as $category) {
    if (
        array_key_exists(
            $category['category'],
            $habitSpending
        )
    ) {
        $habitSpending[
            $category['category']
        ] = (float) $category['amount'];
    }
}

$totalHabitSpending =
    $habitSpending['Cannabis']
    + $habitSpending['Alcohol'];


require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/reports.php';
require_once APP_ROOT . '/includes/footer.php';

