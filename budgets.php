<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Budgets';
$pageType = 'app';
$activePage = 'budgets';

$budgets = [
    [
        'category' => 'Groceries',
        'budget' => 500.00,
        'spent' => 349.21,
    ],
    [
        'category' => 'Fuel',
        'budget' => 250.00,
        'spent' => 148.63,
    ],
    [
        'category' => 'Utilities',
        'budget' => 500.00,
        'spent' => 366.45,
    ],
    [
        'category' => 'Shopping',
        'budget' => 200.00,
        'spent' => 174.10,
    ],
    [
        'category' => 'Cannabis',
        'budget' => 150.00,
        'spent' => 99.40,
    ],
    [
        'category' => 'Alcohol',
        'budget' => 100.00,
        'spent' => 74.82,
    ],
    [
        'category' => 'Entertainment',
        'budget' => 125.00,
        'spent' => 48.50,
    ],
];

$totalBudget = 0.00;
$totalSpent = 0.00;

foreach ($budgets as &$budget) {
    $budget['remaining'] =
        (float) $budget['budget']
        - (float) $budget['spent'];

    $budget['percent'] =
        $budget['budget'] > 0
            ? min(
                100,
                round(
                    ($budget['spent'] / $budget['budget'])
                    * 100
                )
            )
            : 0;

    $totalBudget += (float) $budget['budget'];
    $totalSpent += (float) $budget['spent'];
}

unset($budget);

$totalRemaining = $totalBudget - $totalSpent;

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/budgets.php';
require_once APP_ROOT . '/includes/footer.php';
