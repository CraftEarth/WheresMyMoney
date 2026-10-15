<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Dashboard';
$pageType = 'app';
$activePage = 'dashboard';

$summary = [
    'cash' => 3265.19,
    'income' => 1401.10,
    'spent' => 1247.32,
    'bills' => 1108.58,
    'safe_to_spend' => 816.37,
    'net_worth' => 12842.71,
];

$accounts = [
    [
        'name' => 'Chime Checking',
        'type' => 'Checking',
        'last4' => '1234',
        'balance' => 1465.19,
        'icon' => 'C',
    ],
    [
        'name' => 'United Community Bank',
        'type' => 'Checking',
        'last4' => '5678',
        'balance' => 1800.00,
        'icon' => 'U',
    ],
    [
        'name' => 'Apple Cash',
        'type' => 'Wallet',
        'last4' => '',
        'balance' => 0.00,
        'icon' => 'A',
    ],
    [
        'name' => 'Cash App',
        'type' => 'Wallet',
        'last4' => '',
        'balance' => 0.00,
        'icon' => '$',
    ],
    [
        'name' => 'Cash Wallet',
        'type' => 'Cash',
        'last4' => '',
        'balance' => 0.00,
        'icon' => 'W',
    ],
];

$transactions = [
    [
        'date' => 'Oct 5',
        'name' => 'Social Security Deposit',
        'category' => 'Benefits - Social Security',
        'amount' => 1401.10,
    ],
    [
        'date' => 'Oct 5',
        'name' => 'Truck Payment',
        'category' => 'Auto - Capital One',
        'amount' => -472.13,
    ],
    [
        'date' => 'Oct 1',
        'name' => 'Kentucky Utility',
        'category' => 'Utilities',
        'amount' => -366.45,
    ],
    [
        'date' => 'Oct 1',
        'name' => 'Marathon',
        'category' => 'Fuel',
        'amount' => -64.35,
    ],
    [
        'date' => 'Sep 28',
        'name' => 'Cash App Transfer',
        'category' => 'Transfer',
        'amount' => -70.00,
    ],
    [
        'date' => 'Sep 27',
        'name' => 'Moodytoots',
        'category' => 'Cannabis',
        'amount' => -100.00,
    ],
];

$bills = [
    [
        'name' => 'Truck Payment',
        'amount' => 472.13,
        'due' => 'Oct 5',
        'status' => 'Paid',
    ],
    [
        'name' => 'Kentucky Utility',
        'amount' => 366.45,
        'due' => 'Oct 9',
        'status' => 'Due Soon',
    ],
    [
        'name' => 'Phone Bill',
        'amount' => 100.00,
        'due' => 'Oct 10',
        'status' => 'Upcoming',
    ],
    [
        'name' => 'Netflix',
        'amount' => 15.99,
        'due' => 'Oct 12',
        'status' => 'Upcoming',
    ],
];

$spending = [
    ['name' => 'Groceries', 'amount' => 349.21, 'percent' => 28],
    ['name' => 'Utilities', 'amount' => 274.32, 'percent' => 22],
    ['name' => 'Shopping', 'amount' => 174.10, 'percent' => 14],
    ['name' => 'Fuel', 'amount' => 148.63, 'percent' => 12],
    ['name' => 'Cannabis', 'amount' => 99.40, 'percent' => 8],
    ['name' => 'Alcohol', 'amount' => 74.82, 'percent' => 6],
];

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/dashboard.php';
require_once APP_ROOT . '/includes/footer.php';
