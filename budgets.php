<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Budgets';
$pageType = 'app';
$activePage = 'budgets';

$userId = current_user_id();

$currentMonth = date('Y-m-01');
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

$stmt = db()->prepare(
    '
    SELECT *
    FROM budgets
    WHERE user_id = :user_id
      AND budget_month = :budget_month
      AND is_archived = 0
    ORDER BY category ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
    'budget_month' => $currentMonth,
]);

$budgets = $stmt->fetchAll();


$stmt = db()->prepare(
    '
    SELECT
        category,
        SUM(ABS(amount)) AS spent
    FROM transactions
    WHERE user_id = :user_id
      AND is_archived = 0
      AND transaction_status = "posted"
      AND transaction_type = "expense"
      AND transaction_date
          BETWEEN :month_start AND :month_end
    GROUP BY category
    '
);

$stmt->execute([
    'user_id' => $userId,
    'month_start' => $monthStart,
    'month_end' => $monthEnd,
]);

$spendingRows = $stmt->fetchAll();

$spendingByCategory = [];

foreach ($spendingRows as $row) {
    $spendingByCategory[
        strtolower($row['category'])
    ] = (float) $row['spent'];
}

$totalBudget = 0.00;
$totalSpent = 0.00;

foreach ($budgets as &$budget) {
    $key = strtolower(
        $budget['category']
    );

    $spent = $spendingByCategory[$key]
        ?? 0.00;

    $limit = (float) $budget[
        'monthly_limit'
    ];

    $remaining = $limit - $spent;

    $percent = $limit > 0
        ? ($spent / $limit) * 100
        : 0;

    $budget['spent'] = $spent;
    $budget['remaining'] = $remaining;
    $budget['percent'] = $percent;

    $totalBudget += $limit;
    $totalSpent += $spent;
}

unset($budget);

$totalRemaining =
    $totalBudget - $totalSpent;

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/budgets.php';
require_once APP_ROOT . '/includes/footer.php';
