<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Settings';
$pageType = 'app';
$activePage = 'settings';

$preferences = [
    'currency' => 'USD',
    'date_format' => 'M j, Y',
    'starting_page' => 'Dashboard',
    'include_crypto_net_worth' => true,
    'include_investments_net_worth' => true,
    'include_crypto_safe_to_spend' => false,
    'show_habit_spending' => true,
    'dark_mode' => false,
];

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/settings.php';
require_once APP_ROOT . '/includes/footer.php';
