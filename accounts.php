<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Accounts';
$pageType = 'app';
$activePage = 'accounts';

$userId = current_user_id();

$stmt = db()->prepare(
    '
    SELECT *
    FROM accounts
    WHERE user_id = :user_id
      AND account_status = "active"
    ORDER BY account_name ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$accounts = $stmt->fetchAll();

$totalCash = 0.00;
$totalAvailable = 0.00;
$totalNetWorth = 0.00;

foreach ($accounts as $account) {
    if ((int) $account['include_in_cash'] === 1) {
        $totalCash += (float) $account['balance_current'];
    }

    if ((int) $account['include_in_safe_to_spend'] === 1) {
        $totalAvailable += (float) $account['balance_available'];
    }

    if ((int) $account['include_in_net_worth'] === 1) {
        $totalNetWorth += (float) $account['balance_current'];
    }
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/accounts.php';
require_once APP_ROOT . '/includes/footer.php';
