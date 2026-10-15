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
 * Current month income / spending.
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

$savingsRate =
    $currentIncome > 0
        ? ($netCashFlow / $currentIncome) * 100
        : 0;


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
 * Spending categories.
 */
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
    '
);

$stmt->execute([
    'user_id' => $userId,
    'month_start' => $currentMonthStart,
    'month_end' => $currentMonthEnd,
]);

$categories = $stmt->fetchAll();

foreach ($categories as &$category) {
    $category['percent'] =
        $currentSpending > 0
            ? (
                (float) $category['amount']
                / $currentSpending
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
 * Largest category.
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
