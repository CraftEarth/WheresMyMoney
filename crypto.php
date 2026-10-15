<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Crypto';
$pageType = 'app';
$activePage = 'crypto';

$holdings = [
    [
        'symbol' => 'BTC',
        'name' => 'Bitcoin',
        'quantity' => 0.0184,
        'price' => 67650.00,
        'change' => 2.84,
        'source' => 'Coinbase',
        'network' => 'Bitcoin',
    ],
    [
        'symbol' => 'ETH',
        'name' => 'Ethereum',
        'quantity' => 0.42,
        'price' => 3525.00,
        'change' => 1.66,
        'source' => 'MetaMask',
        'network' => 'Ethereum',
    ],
    [
        'symbol' => 'SOL',
        'name' => 'Solana',
        'quantity' => 4.8,
        'price' => 154.20,
        'change' => -1.14,
        'source' => 'Phantom',
        'network' => 'Solana',
    ],
    [
        'symbol' => 'USDC',
        'name' => 'USD Coin',
        'quantity' => 350.00,
        'price' => 1.00,
        'change' => 0.01,
        'source' => 'Coinbase',
        'network' => 'Base',
    ],
];

$totalValue = 0.00;
$totalChangeValue = 0.00;

foreach ($holdings as &$holding) {
    $holding['value'] =
        (float) $holding['quantity']
        * (float) $holding['price'];

    $holding['change_value'] =
        $holding['value']
        * ((float) $holding['change'] / 100);

    $totalValue += $holding['value'];
    $totalChangeValue += $holding['change_value'];
}

unset($holding);

$totalChangePercent =
    $totalValue > 0
        ? ($totalChangeValue / $totalValue) * 100
        : 0;

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/crypto.php';
require_once APP_ROOT . '/includes/footer.php';
