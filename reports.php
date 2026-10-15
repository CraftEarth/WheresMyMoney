<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Reports';
$pageType = 'app';
$activePage = 'reports';

$monthly = [
    [
        'month' => 'October 2026',
        'income' => 1401.10,
        'spending' => 1247.32,
        'bills' => 1108.58,
    ],
    [
        'month' => 'September 2026',
        'income' => 1401.10,
        'spending' => 1168.44,
        'bills' => 1096.22,
    ],
    [
        'month' => 'August 2026',
        'income' => 1401.10,
        'spending' => 1322.18,
        'bills' => 1108.58,
    ],
];

$categories = [
    ['name' => 'Groceries', 'amount' => 349.21, 'percent' => 28],
    ['name' => 'Utilities', 'amount' => 274.32, 'percent' => 22],
    ['name' => 'Shopping', 'amount' => 174.10, 'percent' => 14],
    ['name' => 'Fuel', 'amount' => 148.63, 'percent' => 12],
    ['name' => 'Cannabis', 'amount' => 99.40, 'percent' => 8],
    ['name' => 'Alcohol', 'amount' => 74.82, 'percent' => 6],
];

$current = $monthly[0];

$netCashFlow =
    (float) $current['income']
    - (float) $current['spending'];

$savingsRate =
    $current['income'] > 0
        ? ($netCashFlow / $current['income']) * 100
        : 0;

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/reports.php';
require_once APP_ROOT . '/includes/footer.php';
