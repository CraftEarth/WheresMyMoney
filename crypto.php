<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Crypto';
$pageType = 'app';
$activePage = 'crypto';

$userId = current_user_id();

$stmt = db()->prepare(
    '
    SELECT *
    FROM crypto_holdings
    WHERE user_id = :user_id
      AND is_archived = 0
    ORDER BY asset_symbol ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$holdings = $stmt->fetchAll();

$totalValue = 0.00;
$totalCostBasis = 0.00;

foreach ($holdings as &$holding) {
    $quantity = (float) $holding['quantity'];
    $price = (float) $holding['current_price'];

    $holding['value'] =
        $quantity * $price;

    $holding['cost_basis'] =
        $holding['average_cost'] !== null
            ? $quantity
                * (float) $holding['average_cost']
            : null;

    $holding['gain_loss'] =
        $holding['cost_basis'] !== null
            ? $holding['value']
                - $holding['cost_basis']
            : null;

    $totalValue += $holding['value'];

    if ($holding['cost_basis'] !== null) {
        $totalCostBasis +=
            $holding['cost_basis'];
    }
}

unset($holding);

$totalGainLoss =
    $totalValue - $totalCostBasis;

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/crypto.php';
require_once APP_ROOT . '/includes/footer.php';
