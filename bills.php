<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Bills';
$pageType = 'app';
$activePage = 'bills';

$bills = [
    [
        'name' => 'Truck Payment',
        'category' => 'Auto',
        'amount' => 472.13,
        'due' => 'Oct 5',
        'frequency' => 'Monthly',
        'account' => 'United Community Bank',
        'autopay' => true,
        'status' => 'Paid',
    ],
    [
        'name' => 'Kentucky Utility',
        'category' => 'Utilities',
        'amount' => 366.45,
        'due' => 'Oct 19',
        'frequency' => 'Monthly',
        'account' => 'Chime Checking',
        'autopay' => false,
        'status' => 'Due Soon',
    ],
    [
        'name' => 'Phone Bill',
        'category' => 'Phone',
        'amount' => 100.00,
        'due' => 'Oct 23',
        'frequency' => 'Monthly',
        'account' => 'Chime Checking',
        'autopay' => true,
        'status' => 'Upcoming',
    ],
    [
        'name' => 'Netflix',
        'category' => 'Subscriptions',
        'amount' => 15.99,
        'due' => 'Oct 27',
        'frequency' => 'Monthly',
        'account' => 'Chime Checking',
        'autopay' => true,
        'status' => 'Upcoming',
    ],
    [
        'name' => 'Internet',
        'category' => 'Utilities',
        'amount' => 79.99,
        'due' => 'Nov 1',
        'frequency' => 'Monthly',
        'account' => 'United Community Bank',
        'autopay' => true,
        'status' => 'Upcoming',
    ],
];

$totalUpcoming = 0.00;
$totalPaid = 0.00;
$dueSoonCount = 0;

foreach ($bills as $bill) {
    if ($bill['status'] === 'Paid') {
        $totalPaid += (float) $bill['amount'];
        continue;
    }

    $totalUpcoming += (float) $bill['amount'];

    if ($bill['status'] === 'Due Soon') {
        $dueSoonCount++;
    }
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/bills.php';
require_once APP_ROOT . '/includes/footer.php';
