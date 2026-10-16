<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Crypto';
$pageType = 'app';
$activePage = 'crypto';

$userId = current_user_id();


/*
|--------------------------------------------------------------------------
| Manual holdings
|--------------------------------------------------------------------------
*/

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

$manualHoldings = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Synced wallet assets
|--------------------------------------------------------------------------
*/

$stmt = db()->prepare(
    '
    SELECT
        a.*,
        g.wallet_label,
        g.wallet_type

    FROM crypto_assets a

    LEFT JOIN crypto_wallet_groups g
        ON g.id = a.wallet_id
       AND g.user_id = a.user_id

    WHERE a.user_id = :user_id
      AND a.is_archived = 0

    ORDER BY
        a.current_value DESC,
        a.symbol ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$syncedAssets = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Normalize both sources into one holdings array
|--------------------------------------------------------------------------
*/

$holdings = [];


/*
 * Manual holdings
 */
foreach ($manualHoldings as $holding) {

    $quantity =
        (float) (
            $holding['quantity']
            ?? 0
        );

    $currentPrice =
        (float) (
            $holding['current_price']
            ?? 0
        );

    $averageCost =
        (float) (
            $holding['average_cost']
            ?? 0
        );

    $value =
        $quantity * $currentPrice;

    $costBasis =
        $quantity * $averageCost;

    $gainLoss =
        $averageCost > 0
            ? $value - $costBasis
            : null;


    $holdings[] = [
        'id' =>
            (int) $holding['id'],

        'source_type' =>
            'manual',

        'asset_symbol' =>
            (string) (
                $holding['asset_symbol']
                ?? ''
            ),

        'asset_name' =>
            (string) (
                $holding['asset_name']
                ?? ''
            ),

        'network' =>
            (string) (
                $holding['network']
                ?? 'Manual'
            ),

        'quantity' =>
            $quantity,

        'current_price' =>
            $currentPrice,

        'source_name' =>
            (string) (
                $holding['source_name']
                ?? 'Manual'
            ),

        'value' =>
            $value,

        'cost_basis' =>
            $costBasis,

        'gain_loss' =>
            $gainLoss,
    ];
}


/*
 * Wallet-synced assets
 */
foreach ($syncedAssets as $asset) {

    $quantity =
        (float) (
            $asset['quantity']
            ?? 0
        );

    $currentPrice =
        (float) (
            $asset['current_price']
            ?? 0
        );

    $currentValue =
        (float) (
            $asset['current_value']
            ?? 0
        );


    $holdings[] = [
        'id' =>
            (int) $asset['id'],

        'source_type' =>
            'wallet',

        'asset_symbol' =>
            (string) (
                $asset['symbol']
                ?? ''
            ),

        'asset_name' =>
            (string) (
                $asset['asset_name']
                ?? ''
            ),

        'network' =>
            (string) (
                $asset['network']
                ?? ''
            ),

        'quantity' =>
            $quantity,

        'current_price' =>
            $currentPrice,

        'source_name' =>
            (string) (
                $asset['wallet_label']
                ?? 'Wallet'
            ),

        'value' =>
            $currentValue,

        'cost_basis' =>
            0,

        'gain_loss' =>
            null,
    ];
}


/*
|--------------------------------------------------------------------------
| Totals
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Crypto display helpers
|--------------------------------------------------------------------------
|
| Crypto is quantity-authoritative:
|
| 0 quantity     = hidden upstream
| non-zero       = always visible
| no USD price   = Unpriced
| tiny USD value = preserve meaningful precision
|
*/

function crypto_display_quantity(
    float $quantity
): string {

    if ($quantity == 0.0) {
        return '0';
    }

    $absolute =
        abs($quantity);

    /*
     * Large token supplies still need readable commas.
     */
    if ($absolute >= 1) {

        $formatted =
            number_format(
                $quantity,
                8,
                '.',
                ','
            );

        return rtrim(
            rtrim(
                $formatted,
                '0'
            ),
            '.'
        );
    }


    /*
     * Crypto dust can legitimately be extremely small.
     * Preserve up to 18 decimal places.
     */
    $formatted =
        number_format(
            $quantity,
            18,
            '.',
            ''
        );

    $formatted =
        rtrim(
            rtrim(
                $formatted,
                '0'
            ),
            '.'
        );


    /*
     * Never visually turn a non-zero holding into zero.
     */
    if (
        $formatted === '' ||
        $formatted === '0'
    ) {
        return '<0.000000000000000001';
    }

    return $formatted;
}


function crypto_display_price(
    float $price
): string {

    if ($price <= 0) {
        return 'Unpriced';
    }

    if ($price >= 1) {
        return '$'
            . number_format(
                $price,
                2
            );
    }

    if ($price >= 0.01) {
        return '$'
            . number_format(
                $price,
                4
            );
    }

    if ($price >= 0.000001) {
        return '$'
            . rtrim(
                rtrim(
                    number_format(
                        $price,
                        8,
                        '.',
                        ''
                    ),
                    '0'
                ),
                '.'
            );
    }

    return '$'
        . rtrim(
            rtrim(
                number_format(
                    $price,
                    12,
                    '.',
                    ''
                ),
                '0'
            ),
            '.'
        );
}


function crypto_display_value(
    float $value,
    float $price,
    float $quantity
): string {

    /*
     * Asset exists, but provider has no reliable price.
     */
    if (
        $quantity > 0 &&
        $price <= 0
    ) {
        return 'Unpriced';
    }


    if ($value >= 0.01) {
        return '$'
            . number_format(
                $value,
                2
            );
    }


    /*
     * Real value below a penny.
     */
    if ($value > 0) {

        if ($value >= 0.000001) {
            return '$'
                . rtrim(
                    rtrim(
                        number_format(
                            $value,
                            8,
                            '.',
                            ''
                        ),
                        '0'
                    ),
                    '.'
                );
        }

        return '$'
            . rtrim(
                rtrim(
                    number_format(
                        $value,
                        12,
                        '.',
                        ''
                    ),
                    '0'
                ),
                '.'
            );
    }


    return '$0.00';
}

/*
 * Hide unpriced crypto from the main portfolio.
 *
 * Keep every asset in crypto_assets so spam tokens,
 * LP receipts, abandoned tokens, and future-priced
 * assets remain available for DeFi/history analysis.
 *
 * Main portfolio = non-zero quantity + real price.
 *
 * BEGIN HIDE UNPRICED
 */
$holdings = array_values(
    array_filter(
        $holdings,
        static function (array $holding): bool {

            $quantity =
                (float) (
                    $holding['quantity']
                    ?? 0
                );

            $price =
                (float) (
                    $holding['current_price']
                    ?? 0
                );

            return
                $quantity > 0 &&
                $price > 0;
        }
    )
);
/* END HIDE UNPRICED */
$totalValue = 0.00;
$totalCostBasis = 0.00;

foreach ($holdings as $holding) {

    $totalValue +=
        (float) (
            $holding['value']
            ?? 0
        );

    $totalCostBasis +=
        (float) (
            $holding['cost_basis']
            ?? 0
        );
}


$totalGainLoss = 0.00;


/*
|--------------------------------------------------------------------------
| Render
|--------------------------------------------------------------------------
*/

require_once APP_ROOT
    . '/includes/header.php';

require_once APP_ROOT
    . '/includes/app-nav.php';

require_once APP_ROOT
    . '/pages/app/crypto.php';

require_once APP_ROOT
    . '/includes/footer.php';
