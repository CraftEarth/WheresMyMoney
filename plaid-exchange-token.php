<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

header('Content-Type: application/json');

$pdo = db();
$userId = current_user_id();

$payload = json_decode(
    file_get_contents('php://input'),
    true
);

$publicToken = trim(
    (string) (
        $payload['public_token']
        ?? ''
    )
);

if ($publicToken === '') {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'stage' => 'input',
        'error' => 'Missing public token.',
    ]);

    exit;
}

$stage = 'exchange';

try {

    /*
     * Exchange the short-lived public token
     * for the permanent Plaid access token.
     */
    $exchange = plaid_request(
        '/item/public_token/exchange',
        [
            'public_token' => $publicToken,
        ]
    );

    $accessToken = (string) (
        $exchange['access_token']
        ?? ''
    );

    $itemId = (string) (
        $exchange['item_id']
        ?? ''
    );

    if (
        $accessToken === '' ||
        $itemId === ''
    ) {
        throw new RuntimeException(
            'Plaid token exchange returned incomplete data.'
        );
    }


    /*
     * Get Item metadata.
     */
    $stage = 'item';

    $itemResponse = plaid_request(
        '/item/get',
        [
            'access_token' => $accessToken,
        ]
    );

    $item = $itemResponse['item'] ?? [];

    $institutionId = isset(
        $item['institution_id']
    )
        ? (string) $item['institution_id']
        : null;

    $institutionName = null;


    /*
     * Resolve institution name when possible.
     * Failure here should not block the connection.
     */
    if ($institutionId !== null) {
        try {
            $institutionResponse =
                plaid_request(
                    '/institutions/get_by_id',
                    [
                        'institution_id' =>
                            $institutionId,

                        'country_codes' => [
                            'US',
                        ],
                    ]
                );

            $institutionName = (
                $institutionResponse[
                    'institution'
                ]['name']
                ?? null
            );
        } catch (Throwable $ignored) {
            $institutionName = null;
        }
    }


    /*
     * Get all accounts selected in Link.
     */
    $stage = 'accounts';

    $accountsResponse = plaid_request(
        '/accounts/get',
        [
            'access_token' =>
                $accessToken,
        ]
    );

    $plaidAccounts =
        $accountsResponse['accounts']
        ?? [];

    if ($plaidAccounts === []) {
        throw new RuntimeException(
            'Plaid returned no accounts.'
        );
    }


    /*
     * Encrypt the permanent access token.
     */
    $stage = 'encrypt';

    $encryptedToken =
        plaid_encrypt_token(
            $accessToken
        );


    /*
     * Persist everything atomically.
     */
    $stage = 'database';

    $pdo->beginTransaction();


    /*
     * Create or update financial connection.
     */
    $stmt = $pdo->prepare(
        '
        SELECT id
        FROM financial_connections
        WHERE user_id = :user_id
          AND provider = "plaid"
          AND provider_item_id = :item_id
        LIMIT 1
        '
    );

    $stmt->execute([
        'user_id' =>
            $userId,

        'item_id' =>
            $itemId,
    ]);

    $connectionId =
        (int) (
            $stmt->fetchColumn()
            ?: 0
        );


    if ($connectionId > 0) {

        $stmt = $pdo->prepare(
            '
            UPDATE financial_connections
            SET
                institution_id =
                    :institution_id,

                institution_name =
                    :institution_name,

                access_token_encrypted =
                    :access_token,

                connection_status =
                    "active"

            WHERE id = :id
              AND user_id = :user_id
            '
        );

        $stmt->execute([
            'institution_id' =>
                $institutionId,

            'institution_name' =>
                $institutionName,

            'access_token' =>
                $encryptedToken,

            'id' =>
                $connectionId,

            'user_id' =>
                $userId,
        ]);

    } else {

        $stmt = $pdo->prepare(
            '
            INSERT INTO financial_connections (
                user_id,
                provider,
                provider_item_id,
                institution_id,
                institution_name,
                access_token_encrypted,
                connection_status
            )
            VALUES (
                :user_id,
                "plaid",
                :item_id,
                :institution_id,
                :institution_name,
                :access_token,
                "active"
            )
            '
        );

        $stmt->execute([
            'user_id' =>
                $userId,

            'item_id' =>
                $itemId,

            'institution_id' =>
                $institutionId,

            'institution_name' =>
                $institutionName,

            'access_token' =>
                $encryptedToken,
        ]);

        $connectionId =
            (int) $pdo->lastInsertId();
    }


    /*
     * Persist each linked Plaid account.
     */
    $savedAccounts = 0;

    foreach ($plaidAccounts as $plaidAccount) {

        $externalAccountId = trim(
            (string) (
                $plaidAccount[
                    'account_id'
                ]
                ?? ''
            )
        );

        if ($externalAccountId === '') {
            continue;
        }


        $plaidType = (string) (
            $plaidAccount['type']
            ?? 'other'
        );

        $plaidSubtype = isset(
            $plaidAccount['subtype']
        )
            ? (string) $plaidAccount[
                'subtype'
            ]
            : null;


        $accountType =
            plaid_account_type(
                $plaidType,
                $plaidSubtype
            );


        $current = (float) (
            $plaidAccount[
                'balances'
            ]['current']
            ?? 0
        );

        $availableRaw =
            $plaidAccount[
                'balances'
            ]['available']
            ?? null;


        /*
         * Credit/loan balances are liabilities.
         */
        if (
            $plaidType === 'credit' ||
            $plaidType === 'loan'
        ) {
            $current = -abs(
                $current
            );
        }


        if ($availableRaw !== null) {
            $available =
                (float) $availableRaw;
        } else {
            $available =
                $current;
        }


        $includeCash =
            $plaidType === 'depository'
                ? 1
                : 0;


        $includeSafeToSpend =
            (
                $plaidType === 'depository'
                &&
                in_array(
                    $plaidSubtype,
                    [
                        'checking',
                        'cash management',
                    ],
                    true
                )
            )
                ? 1
                : 0;


        /*
         * Look for existing copy.
         */
        $stmt = $pdo->prepare(
            '
            SELECT id
            FROM accounts
            WHERE user_id = :user_id
              AND source_type = "linked"
              AND external_account_id =
                    :external_account_id
            LIMIT 1
            '
        );

        $stmt->execute([
            'user_id' =>
                $userId,

            'external_account_id' =>
                $externalAccountId,
        ]);

        $accountId =
            (int) (
                $stmt->fetchColumn()
                ?: 0
            );


        if ($accountId > 0) {

            $stmt = $pdo->prepare(
                '
                UPDATE accounts
                SET
                    financial_connection_id =
                        :connection_id,

                    account_name =
                        :account_name,

                    institution_name =
                        :institution_name,

                    account_type =
                        :account_type,

                    last4 =
                        :last4,

                    balance_current =
                        :balance_current,

                    balance_available =
                        :balance_available,

                    include_in_cash =
                        :include_in_cash,

                    include_in_net_worth =
                        1,

                    include_in_safe_to_spend =
                        :include_safe_to_spend,

                    account_status =
                        "active"

                WHERE id = :id
                  AND user_id = :user_id
                '
            );

            $stmt->execute([
                'connection_id' =>
                    $connectionId,

                'account_name' =>
                    (string) (
                        $plaidAccount['name']
                        ?? 'Linked Account'
                    ),

                'institution_name' =>
                    $institutionName,

                'account_type' =>
                    $accountType,

                'last4' =>
                    $plaidAccount['mask']
                    ?? null,

                'balance_current' =>
                    $current,

                'balance_available' =>
                    $available,

                'include_in_cash' =>
                    $includeCash,

                'include_safe_to_spend' =>
                    $includeSafeToSpend,

                'id' =>
                    $accountId,

                'user_id' =>
                    $userId,
            ]);

        } else {

            $stmt = $pdo->prepare(
                '
                INSERT INTO accounts (
                    user_id,
                    financial_connection_id,
                    external_account_id,
                    account_name,
                    institution_name,
                    account_type,
                    source_type,
                    last4,
                    balance_current,
                    balance_available,
                    include_in_cash,
                    include_in_net_worth,
                    include_in_safe_to_spend,
                    account_status
                )
                VALUES (
                    :user_id,
                    :connection_id,
                    :external_account_id,
                    :account_name,
                    :institution_name,
                    :account_type,
                    "linked",
                    :last4,
                    :balance_current,
                    :balance_available,
                    :include_in_cash,
                    1,
                    :include_safe_to_spend,
                    "active"
                )
                '
            );

            $stmt->execute([
                'user_id' =>
                    $userId,

                'connection_id' =>
                    $connectionId,

                'external_account_id' =>
                    $externalAccountId,

                'account_name' =>
                    (string) (
                        $plaidAccount['name']
                        ?? 'Linked Account'
                    ),

                'institution_name' =>
                    $institutionName,

                'account_type' =>
                    $accountType,

                'last4' =>
                    $plaidAccount['mask']
                    ?? null,

                'balance_current' =>
                    $current,

                'balance_available' =>
                    $available,

                'include_in_cash' =>
                    $includeCash,

                'include_safe_to_spend' =>
                    $includeSafeToSpend,
            ]);
        }

        $savedAccounts++;
    }


    $pdo->commit();


    echo json_encode(
        [
            'success' =>
                true,

            'connection_id' =>
                $connectionId,

            'accounts_saved' =>
                $savedAccounts,
        ],
        JSON_THROW_ON_ERROR
    );

} catch (Throwable $exception) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode(
        [
            'success' =>
                false,

            'stage' =>
                $stage,

            'error' =>
                $exception->getMessage(),
        ],
        JSON_THROW_ON_ERROR
    );
}
