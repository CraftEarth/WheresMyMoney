<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Transactions';
$pageType = 'app';
$activePage = 'transactions';

$transactions = [
    [
        'date' => 'Oct 15',
        'name' => 'Marathon',
        'category' => 'Fuel',
        'account' => 'Chime Checking',
        'amount' => -64.35,
        'status' => 'Posted',
        'type' => 'Expense',
    ],
    [
        'date' => 'Oct 14',
        'name' => 'Social Security Deposit',
        'category' => 'Benefits - Social Security',
        'account' => 'Chime Checking',
        'amount' => 1401.10,
        'status' => 'Posted',
        'type' => 'Income',
    ],
    [
        'date' => 'Oct 12',
        'name' => 'Kentucky Utility',
        'category' => 'Utilities',
        'account' => 'United Community Bank',
        'amount' => -366.45,
        'status' => 'Posted',
        'type' => 'Expense',
    ],
    [
        'date' => 'Oct 10',
        'name' => 'Cash App Transfer',
        'category' => 'Transfer',
        'account' => 'Chime Checking',
        'amount' => -70.00,
        'status' => 'Posted',
        'type' => 'Transfer',
    ],
    [
        'date' => 'Oct 9',
        'name' => 'Moodytoots',
        'category' => 'Cannabis',
        'account' => 'Cash App',
        'amount' => -100.00,
        'status' => 'Posted',
        'type' => 'Expense',
    ],
    [
        'date' => 'Oct 8',
        'name' => 'Truck Payment',
        'category' => 'Auto - Capital One',
        'account' => 'United Community Bank',
        'amount' => -472.13,
        'status' => 'Posted',
        'type' => 'Expense',
    ],
    [
        'date' => 'Oct 7',
        'name' => 'Grocery Store',
        'category' => 'Groceries',
        'account' => 'Chime Checking',
        'amount' => -118.64,
        'status' => 'Posted',
        'type' => 'Expense',
    ],
];

$totalIncome = 0.00;
$totalExpenses = 0.00;
$totalTransfers = 0.00;

foreach ($transactions as $transaction) {
    if ($transaction['type'] === 'Income') {
        $totalIncome += (float) $transaction['amount'];
    }

    if ($transaction['type'] === 'Expense') {
        $totalExpenses += abs((float) $transaction['amount']);
    }

    if ($transaction['type'] === 'Transfer') {
        $totalTransfers += abs((float) $transaction['amount']);
    }
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/transactions.php';
require_once APP_ROOT . '/includes/footer.php';
