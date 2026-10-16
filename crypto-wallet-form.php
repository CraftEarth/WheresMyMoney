<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Add Crypto Wallet';
$pageType = 'app';
$activePage = 'crypto';

$userId = current_user_id();

$errors = [];

$walletType =
    strtolower(
        trim(
            (string) (
                $_POST['wallet_type']
                ?? $_GET['type']
                ?? 'metamask'
            )
        )
    );

if (
    !in_array(
        $walletType,
        [
            'metamask',
            'phantom',
        ],
        true
    )
) {
    $walletType = 'metamask';
}


$walletLabel = trim(
    (string) (
        $_POST['wallet_label']
        ?? (
            $walletType === 'phantom'
                ? 'My Phantom'
                : 'My MetaMask'
        )
    )
);


$evmAddress = trim(
    (string) (
        $_POST['evm_address']
        ?? ''
    )
);

$solanaAddress = trim(
    (string) (
        $_POST['solana_address']
        ?? ''
    )
);

$bitcoinAddress = trim(
    (string) (
        $_POST['bitcoin_address']
        ?? ''
    )
);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !hash_equals(
            csrf_token(),
            (string) (
                $_POST['csrf_token']
                ?? ''
            )
        )
    ) {
        $errors[] =
            'Your session token expired. Refresh and try again.';
    }


    if ($walletLabel === '') {
        $errors[] =
            'Wallet label is required.';
    }


    /*
    |--------------------------------------------------------------------------
    | Validate public addresses
    |--------------------------------------------------------------------------
    */

    if (
        $evmAddress !== '' &&
        !preg_match(
            '/^0x[a-fA-F0-9]{40}$/',
            $evmAddress
        )
    ) {
        $errors[] =
            'The EVM address does not look valid.';
    }


    if (
        $solanaAddress !== '' &&
        !preg_match(
            '/^[1-9A-HJ-NP-Za-km-z]{32,44}$/',
            $solanaAddress
        )
    ) {
        $errors[] =
            'The Solana address does not look valid.';
    }


    if (
        $bitcoinAddress !== '' &&
        !preg_match(
            '/^(bc1|[13])[a-zA-HJ-NP-Z0-9]{20,90}$/i',
            $bitcoinAddress
        )
    ) {
        $errors[] =
            'The Bitcoin address does not look valid.';
    }


    if (
        $evmAddress === '' &&
        $solanaAddress === '' &&
        $bitcoinAddress === ''
    ) {
        $errors[] =
            'Enter at least one public wallet address.';
    }


    /*
    |--------------------------------------------------------------------------
    | Duplicate address checks
    |--------------------------------------------------------------------------
    */

    $addressChecks = [
        'evm' =>
            $evmAddress,

        'solana' =>
            $solanaAddress,

        'bitcoin' =>
            $bitcoinAddress,
    ];


    foreach (
        $addressChecks
        as $addressType => $address
    ) {

        if ($address === '') {
            continue;
        }


        $stmt = db()->prepare(
            '
            SELECT
                cwa.id,
                cwg.wallet_label

            FROM crypto_wallet_addresses cwa

            INNER JOIN crypto_wallet_groups cwg
                ON cwg.id =
                    cwa.wallet_group_id

            WHERE cwa.user_id =
                    :user_id

              AND cwa.address_type =
                    :address_type

              AND LOWER(
                    cwa.wallet_address
                  ) =
                    LOWER(:wallet_address)

              AND cwa.address_status =
                    "active"

            LIMIT 1
            '
        );

        $stmt->execute([
            'user_id' =>
                $userId,

            'address_type' =>
                $addressType,

            'wallet_address' =>
                $address,
        ]);


        $duplicate =
            $stmt->fetch();


        if ($duplicate) {

            $errors[] =
                ucfirst($addressType)
                . ' address is already connected to '
                . (
                    $duplicate['wallet_label']
                    ?? 'another wallet'
                )
                . '.';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Save wallet group + addresses
    |--------------------------------------------------------------------------
    */

    if ($errors === []) {

        $pdo = db();

        $pdo->beginTransaction();

        try {

            $stmt = $pdo->prepare(
                '
                INSERT INTO crypto_wallet_groups (
                    user_id,
                    wallet_type,
                    wallet_label,
                    wallet_status
                )
                VALUES (
                    :user_id,
                    :wallet_type,
                    :wallet_label,
                    "active"
                )
                '
            );

            $stmt->execute([
                'user_id' =>
                    $userId,

                'wallet_type' =>
                    $walletType,

                'wallet_label' =>
                    $walletLabel,
            ]);


            $walletGroupId =
                (int) $pdo->lastInsertId();


            $insertAddress =
                $pdo->prepare(
                    '
                    INSERT INTO crypto_wallet_addresses (
                        user_id,
                        wallet_group_id,
                        address_type,
                        wallet_address,
                        address_label,
                        address_status
                    )
                    VALUES (
                        :user_id,
                        :wallet_group_id,
                        :address_type,
                        :wallet_address,
                        :address_label,
                        "active"
                    )
                    '
                );


            foreach (
                $addressChecks
                as $addressType => $address
            ) {

                if ($address === '') {
                    continue;
                }


                $label = match (
                    $addressType
                ) {

                    'evm' =>
                        'EVM / Ethereum',

                    'solana' =>
                        'Solana',

                    'bitcoin' =>
                        'Bitcoin',

                    default =>
                        ucfirst(
                            $addressType
                        ),
                };


                $insertAddress->execute([
                    'user_id' =>
                        $userId,

                    'wallet_group_id' =>
                        $walletGroupId,

                    'address_type' =>
                        $addressType,

                    'wallet_address' =>
                        $address,

                    'address_label' =>
                        $label,
                ]);
            }


            $pdo->commit();


            header(
                'Location: '
                . url(
                    'crypto.php?wallet_added=1'
                )
            );

            exit;


        } catch (Throwable $exception) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errors[] =
                'Wallet could not be saved: '
                . $exception->getMessage();
        }
    }
}


require_once APP_ROOT
    . '/includes/header.php';

require_once APP_ROOT
    . '/includes/app-nav.php';

require_once APP_ROOT
    . '/pages/app/crypto-wallet-form.php';

require_once APP_ROOT
    . '/includes/footer.php';
