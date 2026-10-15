<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Bills';
$pageType = 'app';
$activePage = 'bills';

$userId = current_user_id();
$today = date('Y-m-d');

$stmt = db()->prepare(
    '
    SELECT
        b.*,
        a.account_name
    FROM bills b
    LEFT JOIN accounts a
        ON a.id = b.account_id
       AND a.user_id = b.user_id
    WHERE b.user_id = :user_id
      AND b.is_archived = 0
    ORDER BY
        b.next_due_date ASC,
        b.bill_name ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$bills = $stmt->fetchAll();

$totalUpcoming = 0.00;
$totalPaid = 0.00;
$dueSoonCount = 0;

foreach ($bills as $bill) {
    $amount = (float) $bill['expected_amount'];

    if ($bill['bill_status'] === 'paid') {
        $totalPaid += $amount;
        continue;
    }

    if (
        in_array(
            $bill['bill_status'],
            [
                'upcoming',
                'due_soon',
                'overdue',
            ],
            true
        )
    ) {
        $totalUpcoming += $amount;
    }

    if (
        $bill['bill_status'] === 'due_soon'
    ) {
        $dueSoonCount++;
    }
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/bills.php';
require_once APP_ROOT . '/includes/footer.php';
