<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Accounts';
$pageType = 'app';
$activePage = 'accounts';

$accounts = [
    [
        'name' => 'Chime Checking',
        'institution' => 'Chime',
        'type' => 'Checking',
        'last4' => '1234',
        'balance' => 1465.19,
        'available' => 1465.19,
        'source' => 'Manual',
        'icon' => 'C',
    ],
    [
        'name' => 'United Community Bank',
        'institution' => 'United Community Bank',
        'type' => 'Checking',
        'last4' => '5678',
        'balance' => 1800.00,
        'available' => 1800.00,
        'source' => 'Manual',
        'icon' => 'U',
    ],
    [
        'name' => 'Apple Cash',
        'institution' => 'Apple',
        'type' => 'Wallet',
        'last4' => '',
        'balance' => 0.00,
        'available' => 0.00,
        'source' => 'Manual',
        'icon' => 'A',
    ],
    [
        'name' => 'Cash App',
        'institution' => 'Cash App',
        'type' => 'Wallet',
        'last4' => '',
        'balance' => 0.00,
        'available' => 0.00,
        'source' => 'Manual',
        'icon' => '$',
    ],
    [
        'name' => 'Cash Wallet',
        'institution' => 'Physical Cash',
        'type' => 'Cash',
        'last4' => '',
        'balance' => 0.00,
        'available' => 0.00,
        'source' => 'Manual',
        'icon' => 'W',
    ],
];

$cashTotal = array_sum(
    array_column(
        $accounts,
        'balance'
    )
);

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/accounts.php';
require_once APP_ROOT . '/includes/footer.php';
