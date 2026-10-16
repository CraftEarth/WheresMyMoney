<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

header('Content-Type: text/plain');

$userId = current_user_id();
$walletId = (int) ($_GET['wallet_id'] ?? 0);

if ($walletId <= 0) {
    http_response_code(400);
    echo 'Invalid wallet.';
    exit;
}


/*
|--------------------------------------------------------------------------
| Wallet
|--------------------------------------------------------------------------
*/

$stmt = db()->prepare(
    '
    SELECT *
    FROM crypto_wallet_groups
    WHERE id = :wallet_id
      AND user_id = :user_id
      AND wallet_status = "active"
    LIMIT 1
    '
);

$stmt->execute([
    'wallet_id' => $walletId,
    'user_id' => $userId,
]);

$wallet = $stmt->fetch();

if (!$wallet) {
    http_response_code(404);
    echo 'Wallet not found.';
    exit;
}


/*
|--------------------------------------------------------------------------
| Public addresses
|--------------------------------------------------------------------------
*/

$stmt = db()->prepare(
    '
    SELECT *
    FROM crypto_wallet_addresses
    WHERE wallet_group_id = :wallet_id
      AND user_id = :user_id
      AND address_status = "active"
    ORDER BY id ASC
    '
);

$stmt->execute([
    'wallet_id' => $walletId,
    'user_id' => $userId,
]);

$walletAddresses = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Alchemy Portfolio request
|--------------------------------------------------------------------------
*/

$config = alchemy_config();

$apiKey = trim(
    (string) $config['api_key']
);

$requests = [];

foreach ($walletAddresses as $address) {

    $type =
        (string) $address['address_type'];

    $value =
        trim(
            (string) $address[
                'wallet_address'
            ]
        );

    if ($type === 'evm') {

        $requests[] = [
            'address' => $value,
            'networks' => [
                'eth-mainnet',
            ],
        ];
    }

    if ($type === 'solana') {

        $requests[] = [
            'address' => $value,
            'networks' => [
                'solana-mainnet',
            ],
        ];
    }
}


if ($requests === []) {
    echo 'No supported wallet addresses found.';
    exit;
}


$url =
    'https://api.g.alchemy.com/data/v1/'
    . rawurlencode($apiKey)
    . '/assets/tokens/by-address';


$payload = [
    'addresses' =>
        $requests,

    'withMetadata' =>
        true,

    'withPrices' =>
        true,

    'includeNativeTokens' =>
        true,

    'includeErc20Tokens' =>
        true,

    'includeBlockMetadata' =>
        false,
];


$ch = curl_init($url);

curl_setopt_array(
    $ch,
    [
        CURLOPT_POST =>
            true,

        CURLOPT_RETURNTRANSFER =>
            true,

        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
        ],

        CURLOPT_POSTFIELDS =>
            json_encode(
                $payload,
                JSON_THROW_ON_ERROR
            ),

        CURLOPT_TIMEOUT =>
            60,
    ]
);


$responseBody = curl_exec($ch);

if ($responseBody === false) {

    $error = curl_error($ch);

    curl_close($ch);

    http_response_code(500);

    echo 'Alchemy request failed: '
        . $error;

    exit;
}


$status = (int) curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

curl_close($ch);


if (
    $status < 200 ||
    $status >= 300
) {
    http_response_code(500);

    echo 'Alchemy HTTP error '
        . $status
        . PHP_EOL
        . $responseBody;

    exit;
}


$response = json_decode(
    $responseBody,
    true,
    512,
    JSON_THROW_ON_ERROR
);


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function crypto_hex_to_float(
    string $hex
): float {

    $hex = strtolower(
        preg_replace(
            '/^0x/',
            '',
            trim($hex)
        ) ?? ''
    );

    if ($hex === '') {
        return 0.0;
    }

    $number = 0.0;

    foreach (
        str_split($hex)
        as $character
    ) {
        $number =
            ($number * 16)
            + hexdec($character);
    }

    return $number;
}


function crypto_quantity(
    string $hex,
    int $decimals
): float {

    $raw =
        crypto_hex_to_float(
            $hex
        );

    if ($decimals <= 0) {
        return $raw;
    }

    return $raw
        / (10 ** $decimals);
}


function crypto_usd_price(
    array $token
): float {

    foreach (
        $token['tokenPrices'] ?? []
        as $price
    ) {

        if (
            strtolower(
                (string) (
                    $price['currency']
                    ?? ''
                )
            ) === 'usd'
        ) {
            return (float) (
                $price['value']
                ?? 0
            );
        }
    }

    return 0.0;
}


/*
 * Native coins are authoritative by network.
 *
 * Never rely on incomplete token metadata to decide
 * native decimal precision.
 */
function crypto_native_metadata(
    string $network
): ?array {

    return match ($network) {

        'eth-mainnet' => [
            'symbol' =>
                'ETH',

            'name' =>
                'Ethereum',

            'decimals' =>
                18,
        ],

        'solana-mainnet' => [
            'symbol' =>
                'SOL',

            'name' =>
                'Solana',

            'decimals' =>
                9,
        ],

        default =>
            null,
    };
}


/*
|--------------------------------------------------------------------------
| Parse returned portfolio
|--------------------------------------------------------------------------
*/

$tokens =
    $response['data']['tokens']
    ?? [];

$assets = [];
$ignored = [];


foreach ($tokens as $token) {

    $network = trim(
        (string) (
            $token['network']
            ?? ''
        )
    );


    $contractAddress =
        $token['tokenAddress']
        ?? null;


    $isNative =
        $contractAddress === null ||
        trim(
            (string) $contractAddress
        ) === '';


    $metadata =
        is_array(
            $token['tokenMetadata']
            ?? null
        )
            ? $token['tokenMetadata']
            : [];


    /*
    |--------------------------------------------------------------------------
    | Native currency
    |--------------------------------------------------------------------------
    */

    if ($isNative) {

        $native =
            crypto_native_metadata(
                $network
            );

        if ($native === null) {

            $ignored[] =
                'Unsupported native network: '
                . $network;

            continue;
        }


        $symbol =
            $native['symbol'];

        $name =
            $native['name'];

        $decimals =
            (int) $native['decimals'];


    /*
    |--------------------------------------------------------------------------
    | Contract token
    |--------------------------------------------------------------------------
    */

    } else {

        $symbol = trim(
            (string) (
                $metadata['symbol']
                ?? ''
            )
        );

        $name = trim(
            (string) (
                $metadata['name']
                ?? ''
            )
        );

        $decimals =
            isset(
                $metadata['decimals']
            )
                ? (int) $metadata[
                    'decimals'
                ]
                : -1;


        /*
         * A token without known decimals cannot be
         * safely converted from raw units.
         *
         * Keep it out of accounting until metadata
         * can be resolved.
         */
        if ($decimals < 0) {

            $ignored[] =
                ($symbol !== ''
                    ? $symbol
                    : 'Unknown token')
                . ' skipped: missing decimals';

            continue;
        }


        if ($symbol === '') {

            $ignored[] =
                'Token '
                . substr(
                    (string) $contractAddress,
                    0,
                    12
                )
                . '... skipped: missing symbol';

            continue;
        }


        if ($name === '') {
            $name = $symbol;
        }
    }


    $balanceHex =
        (string) (
            $token['tokenBalance']
            ?? '0x0'
        );


    $quantity =
        crypto_quantity(
            $balanceHex,
            $decimals
        );


    if (
        !is_finite($quantity) ||
        $quantity <= 0
    ) {
        continue;
    }


    $price =
        crypto_usd_price(
            $token
        );


    /*
     * Never manufacture a dollar valuation.
     *
     * No provider price = $0 value until a reliable
     * price source is available.
     */
    $value =
        $price > 0
            ? $quantity * $price
            : 0.0;


    /*
     * Last-resort sanity guard.
     *
     * An unknown malformed balance must never turn
     * a personal-finance portfolio into quadrillions.
     */
    if (
        !is_finite($value) ||
        $value < 0
    ) {

        $ignored[] =
            $symbol
            . ' skipped: invalid valuation';

        continue;
    }


    $assetType =
        $isNative
            ? 'native'
            : (
                $network ===
                    'solana-mainnet'
                    ? 'spl'
                    : 'erc20'
            );


    $assets[] = [
        'network' =>
            $network,

        'asset_type' =>
            $assetType,

        'contract_address' =>
            $isNative
                ? null
                : (string) $contractAddress,

        'symbol' =>
            $symbol,

        'asset_name' =>
            $name,

        'quantity' =>
            $quantity,

        'current_price' =>
            $price,

        'current_value' =>
            $value,

        'external_asset_id' =>
            $isNative
                ? $network
                    . ':native'
                : strtolower(
                    (string) $contractAddress
                ),
    ];
}


/*
|--------------------------------------------------------------------------
| Persist snapshot
|--------------------------------------------------------------------------
*/

$pdo = db();

$pdo->beginTransaction();

try {

    $stmt = $pdo->prepare(
        '
        DELETE FROM crypto_assets
        WHERE user_id = :user_id
          AND wallet_id = :wallet_id
        '
    );

    $stmt->execute([
        'user_id' =>
            $userId,

        'wallet_id' =>
            $walletId,
    ]);


    $insert = $pdo->prepare(
        '
        INSERT INTO crypto_assets (
            user_id,
            wallet_id,
            network,
            asset_type,
            contract_address,
            symbol,
            asset_name,
            quantity,
            current_price,
            current_value,
            external_asset_id,
            is_archived
        )
        VALUES (
            :user_id,
            :wallet_id,
            :network,
            :asset_type,
            :contract_address,
            :symbol,
            :asset_name,
            :quantity,
            :current_price,
            :current_value,
            :external_asset_id,
            0
        )
        '
    );


    foreach ($assets as $asset) {

        $insert->execute([
            'user_id' =>
                $userId,

            'wallet_id' =>
                $walletId,

            'network' =>
                $asset['network'],

            'asset_type' =>
                $asset[
                    'asset_type'
                ],

            'contract_address' =>
                $asset[
                    'contract_address'
                ],

            'symbol' =>
                $asset['symbol'],

            'asset_name' =>
                $asset[
                    'asset_name'
                ],

            'quantity' =>
                $asset['quantity'],

            'current_price' =>
                $asset[
                    'current_price'
                ],

            'current_value' =>
                $asset[
                    'current_value'
                ],

            'external_asset_id' =>
                $asset[
                    'external_asset_id'
                ],
        ]);
    }


    $stmt = $pdo->prepare(
        '
        UPDATE crypto_wallet_groups
        SET last_synced_at = NOW()
        WHERE id = :wallet_id
          AND user_id = :user_id
        '
    );

    $stmt->execute([
        'wallet_id' =>
            $walletId,

        'user_id' =>
            $userId,
    ]);


    $pdo->commit();

} catch (Throwable $exception) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo 'Crypto save failed: '
        . $exception->getMessage();

    exit;
}


/*
|--------------------------------------------------------------------------
| Result
|--------------------------------------------------------------------------
*/

echo 'Wallet portfolio sync complete.'
    . PHP_EOL;

echo 'Assets stored: '
    . count($assets)
    . PHP_EOL;


$totalValue = 0.0;


foreach ($assets as $asset) {

    $totalValue +=
        (float) $asset[
            'current_value'
        ];


    echo PHP_EOL;

    echo $asset['symbol']
        . ': '
        . number_format(
            (float) $asset[
                'quantity'
            ],
            8
        )
        . PHP_EOL;


    echo 'Price: $'
        . number_format(
            (float) $asset[
                'current_price'
            ],
            4
        )
        . PHP_EOL;


    echo 'Value: $'
        . number_format(
            (float) $asset[
                'current_value'
            ],
            2
        )
        . PHP_EOL;
}


echo PHP_EOL;

echo 'Portfolio value: $'
    . number_format(
        $totalValue,
        2
    )
    . PHP_EOL;


if ($ignored !== []) {

    echo PHP_EOL;
    echo 'Ignored / review:'
        . PHP_EOL;

    foreach ($ignored as $message) {

        echo '- '
            . $message
            . PHP_EOL;
    }
}


$partialErrors =
    $response['error'][
        'partialErrors'
    ]
    ?? [];


if ($partialErrors !== []) {

    echo PHP_EOL;
    echo 'Provider warnings:'
        . PHP_EOL;

    foreach (
        $partialErrors
        as $error
    ) {

        echo '- '
            . (
                is_array($error)
                    ? json_encode(
                        $error
                    )
                    : (string) $error
            )
            . PHP_EOL;
    }
}
