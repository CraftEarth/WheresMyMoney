<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$userId = current_user_id();

$holdingId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$holding = null;
$errorMessage = null;

if ($holdingId > 0) {
    $stmt = db()->prepare(
        '
        SELECT *
        FROM crypto_holdings
        WHERE id = :id
          AND user_id = :user_id
          AND is_archived = 0
        LIMIT 1
        '
    );

    $stmt->execute([
        'id' => $holdingId,
        'user_id' => $userId,
    ]);

    $holding = $stmt->fetch();

    if (!$holding) {
        http_response_code(404);
        exit('Crypto holding not found.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !verify_csrf(
            $_POST['csrf_token'] ?? null
        )
    ) {
        $errorMessage =
            'The form expired. Please try again.';
    } else {
        $symbol = strtoupper(
            trim(
                (string) ($_POST['asset_symbol'] ?? '')
            )
        );

        $name = trim(
            (string) ($_POST['asset_name'] ?? '')
        );

        $network = trim(
            (string) ($_POST['network'] ?? '')
        );

        $source = trim(
            (string) ($_POST['source_name'] ?? '')
        );

        $wallet = trim(
            (string) ($_POST['wallet_address'] ?? '')
        );

        $quantity = max(
            0,
            (float) ($_POST['quantity'] ?? 0)
        );

        $averageCostRaw = trim(
            (string) ($_POST['average_cost'] ?? '')
        );

        $averageCost = $averageCostRaw !== ''
            ? max(
                0,
                (float) $averageCostRaw
            )
            : null;

        $currentPrice = max(
            0,
            (float) ($_POST['current_price'] ?? 0)
        );

        if ($symbol === '') {
            $errorMessage =
                'Asset symbol is required.';
        }

        if ($name === '') {
            $errorMessage =
                'Asset name is required.';
        }

        if ($quantity < 0) {
            $errorMessage =
                'Quantity cannot be negative.';
        }

        if ($errorMessage === null) {
            if ($holdingId > 0) {
                $stmt = db()->prepare(
                    '
                    UPDATE crypto_holdings
                    SET
                        asset_symbol = :asset_symbol,
                        asset_name = :asset_name,
                        network = :network,
                        source_name = :source_name,
                        wallet_address = :wallet_address,
                        quantity = :quantity,
                        average_cost = :average_cost,
                        current_price = :current_price
                    WHERE id = :id
                      AND user_id = :user_id
                    '
                );

                $stmt->execute([
                    'asset_symbol' => $symbol,
                    'asset_name' => $name,
                    'network' => $network !== ''
                        ? $network
                        : null,
                    'source_name' => $source !== ''
                        ? $source
                        : null,
                    'wallet_address' => $wallet !== ''
                        ? $wallet
                        : null,
                    'quantity' => $quantity,
                    'average_cost' => $averageCost,
                    'current_price' => $currentPrice,
                    'id' => $holdingId,
                    'user_id' => $userId,
                ]);
            } else {
                $stmt = db()->prepare(
                    '
                    INSERT INTO crypto_holdings (
                        user_id,
                        asset_symbol,
                        asset_name,
                        network,
                        source_name,
                        wallet_address,
                        quantity,
                        average_cost,
                        current_price
                    )
                    VALUES (
                        :user_id,
                        :asset_symbol,
                        :asset_name,
                        :network,
                        :source_name,
                        :wallet_address,
                        :quantity,
                        :average_cost,
                        :current_price
                    )
                    '
                );

                $stmt->execute([
                    'user_id' => $userId,
                    'asset_symbol' => $symbol,
                    'asset_name' => $name,
                    'network' => $network !== ''
                        ? $network
                        : null,
                    'source_name' => $source !== ''
                        ? $source
                        : null,
                    'wallet_address' => $wallet !== ''
                        ? $wallet
                        : null,
                    'quantity' => $quantity,
                    'average_cost' => $averageCost,
                    'current_price' => $currentPrice,
                ]);
            }

            header(
                'Location: ' . url('crypto.php')
            );

            exit;
        }
    }
}

$pageTitle = $holdingId > 0
    ? 'Edit Crypto'
    : 'Add Crypto';

$pageType = 'app';
$activePage = 'crypto';

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/crypto-form.php';
require_once APP_ROOT . '/includes/footer.php';
