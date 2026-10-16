<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

header('Content-Type: text/plain');

$userId = current_user_id();
$pdo = db();


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function plaid_local_account_id(
    PDO $pdo,
    int $userId,
    string $externalAccountId
): ?int {
    $stmt = $pdo->prepare(
        '
        SELECT id
        FROM accounts
        WHERE user_id = :user_id
          AND source_type = "linked"
          AND external_account_id = :external_account_id
          AND account_status = "active"
        LIMIT 1
        '
    );

    $stmt->execute([
        'user_id' => $userId,
        'external_account_id' => $externalAccountId,
    ]);

    $id = $stmt->fetchColumn();

    return $id !== false
        ? (int) $id
        : null;
}


function plaid_signed_amount(
    array $transaction
): float {
    /*
     * Plaid:
     * positive = money leaving
     * negative = money entering
     *
     * Our ledger:
     * negative = outgoing
     * positive = incoming
     */
    return -1 * (float) (
        $transaction['amount']
        ?? 0
    );
}


function plaid_transaction_category(
    array $transaction
): string {
    $primary = trim(
        (string) (
            $transaction[
                'personal_finance_category'
            ]['primary']
            ?? ''
        )
    );

    if ($primary !== '') {
        return ucwords(
            strtolower(
                str_replace(
                    '_',
                    ' ',
                    $primary
                )
            )
        );
    }

    return 'Uncategorized';
}


/*
 * Convert Plaid account balance into our
 * asset/liability convention.
 *
 * Depository:
 *   $200 balance = +200 asset
 *
 * Credit / loan:
 *   $200 owed = -200 liability
 */
function plaid_current_balance(
    array $account
): float {
    $current = (float) (
        $account['balances']['current']
        ?? 0
    );

    $type = strtolower(
        trim(
            (string) (
                $account['type']
                ?? ''
            )
        )
    );

    if (
        $type === 'credit' ||
        $type === 'loan'
    ) {
        return -abs($current);
    }

    return $current;
}


function plaid_available_balance(
    array $account,
    float $currentBalance
): float {
    $available =
        $account['balances']['available']
        ?? null;

    if ($available !== null) {
        return (float) $available;
    }

    /*
     * Plaid does not always supply available.
     * For asset accounts, current is the safest fallback.
     *
     * For liabilities this field isn't used in
     * safe-to-spend anyway, so keep it positive.
     */
    return abs($currentBalance);
}


/*
|--------------------------------------------------------------------------
| Load active connections
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare(
    '
    SELECT
        id,
        provider_item_id,
        access_token_encrypted,
        sync_cursor
    FROM financial_connections
    WHERE user_id = :user_id
      AND provider = "plaid"
      AND connection_status = "active"
    ORDER BY id ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$connections = $stmt->fetchAll();

if ($connections === []) {
    echo 'No active Plaid connections found.';
    exit;
}


/*
|--------------------------------------------------------------------------
| Totals
|--------------------------------------------------------------------------
*/

$totalAccountsUpdated = 0;
$totalAdded = 0;
$totalModified = 0;
$totalRemoved = 0;
$totalSkipped = 0;
$totalConnections = 0;


/*
|--------------------------------------------------------------------------
| Sync every Plaid Item
|--------------------------------------------------------------------------
*/

foreach ($connections as $connection) {

    $connectionId =
        (int) $connection['id'];

    $cursor =
        $connection['sync_cursor'];

    $accessToken =
        plaid_decrypt_token(
            (string) $connection[
                'access_token_encrypted'
            ]
        );

    $totalConnections++;


    /*
    |--------------------------------------------------------------------------
    | 1. Refresh account balances
    |--------------------------------------------------------------------------
    |
    | Try Plaid's balance endpoint first.
    | If the current Plaid plan/institution does not support it,
    | fall back to /accounts/get.
    |
    */

    try {
        try {
            $accountResponse =
                plaid_request(
                    '/accounts/balance/get',
                    [
                        'access_token' =>
                            $accessToken,
                    ]
                );

        } catch (Throwable $balanceException) {

            $accountResponse =
                plaid_request(
                    '/accounts/get',
                    [
                        'access_token' =>
                            $accessToken,
                    ]
                );
        }


        foreach (
            $accountResponse['accounts'] ?? []
            as $plaidAccount
        ) {
            $externalAccountId = trim(
                (string) (
                    $plaidAccount['account_id']
                    ?? ''
                )
            );

            if ($externalAccountId === '') {
                $totalSkipped++;
                continue;
            }


            $currentBalance =
                plaid_current_balance(
                    $plaidAccount
                );

            $availableBalance =
                plaid_available_balance(
                    $plaidAccount,
                    $currentBalance
                );


            $stmt = $pdo->prepare(
                '
                UPDATE accounts
                SET
                    balance_current =
                        :balance_current,

                    balance_available =
                        :balance_available,

                    updated_at =
                        NOW()

                WHERE user_id =
                        :user_id

                  AND financial_connection_id =
                        :connection_id

                  AND external_account_id =
                        :external_account_id

                  AND source_type =
                        "linked"

                  AND account_status =
                        "active"
                '
            );

            $stmt->execute([
                'balance_current' =>
                    $currentBalance,

                'balance_available' =>
                    $availableBalance,

                'user_id' =>
                    $userId,

                'connection_id' =>
                    $connectionId,

                'external_account_id' =>
                    $externalAccountId,
            ]);


            if ($stmt->rowCount() > 0) {
                $totalAccountsUpdated++;
            }
        }

    } catch (Throwable $exception) {

        echo 'WARNING: balance refresh failed for connection '
            . $connectionId
            . ': '
            . $exception->getMessage()
            . PHP_EOL;
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Pull transaction changes
    |--------------------------------------------------------------------------
    */

    $hasMore = true;

    $added = [];
    $modified = [];
    $removed = [];

    try {

        while ($hasMore) {

            $payload = [
                'access_token' =>
                    $accessToken,

                'count' =>
                    500,

                'options' => [
                    'include_original_description' =>
                        true,
                ],
            ];


            if (
                $cursor !== null &&
                $cursor !== ''
            ) {
                $payload['cursor'] =
                    $cursor;
            }


            $response =
                plaid_request(
                    '/transactions/sync',
                    $payload
                );


            foreach (
                $response['added'] ?? []
                as $item
            ) {
                $added[] = $item;
            }


            foreach (
                $response['modified'] ?? []
                as $item
            ) {
                $modified[] = $item;
            }


            foreach (
                $response['removed'] ?? []
                as $item
            ) {
                $removed[] = $item;
            }


            $cursor = (string) (
                $response['next_cursor']
                ?? $cursor
                ?? ''
            );


            $hasMore = (bool) (
                $response['has_more']
                ?? false
            );
        }

    } catch (Throwable $exception) {

        echo 'ERROR reading transactions for connection '
            . $connectionId
            . ': '
            . $exception->getMessage()
            . PHP_EOL;

        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Persist transaction changes atomically
    |--------------------------------------------------------------------------
    */

    $pdo->beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | Added
        |--------------------------------------------------------------------------
        */

        foreach ($added as $plaidTx) {

            $externalAccountId = trim(
                (string) (
                    $plaidTx['account_id']
                    ?? ''
                )
            );

            $externalTransactionId = trim(
                (string) (
                    $plaidTx['transaction_id']
                    ?? ''
                )
            );


            if (
                $externalAccountId === '' ||
                $externalTransactionId === ''
            ) {
                $totalSkipped++;
                continue;
            }


            $accountId =
                plaid_local_account_id(
                    $pdo,
                    $userId,
                    $externalAccountId
                );


            if ($accountId === null) {
                $totalSkipped++;
                continue;
            }


            $rawDescription = trim(
                (string) (
                    $plaidTx[
                        'original_description'
                    ]
                    ?? $plaidTx['name']
                    ?? 'Plaid Transaction'
                )
            );


            $fallbackDescription = trim(
                (string) (
                    $plaidTx['merchant_name']
                    ?? $plaidTx['name']
                    ?? $rawDescription
                )
            );


            $providerCategory =
                plaid_transaction_category(
                    $plaidTx
                );


            $normalized =
                normalize_transaction(
                    $pdo,
                    $userId,
                    $rawDescription,
                    $fallbackDescription,
                    $providerCategory
                );


            $amount =
                plaid_signed_amount(
                    $plaidTx
                );


            $classification =
                transaction_classification(
                    $rawDescription,
                    $normalized['category'],
                    $amount
                );


            $transactionType =
                $classification[
                    'transaction_type'
                ];


            $category =
                $classification[
                    'category'
                ];


            $isTransfer =
                (int) $classification[
                    'is_transfer'
                ];


            $needsReview =
                $normalized['matched']
                    ? 0
                    : (int) $classification[
                        'needs_review'
                    ];


            $status =
                !empty(
                    $plaidTx['pending']
                )
                    ? 'pending'
                    : 'posted';


            $pendingTransactionId =
                $plaidTx[
                    'pending_transaction_id'
                ]
                ?? null;


            $stmt = $pdo->prepare(
                '
                INSERT INTO transactions (
                    user_id,
                    account_id,
                    external_transaction_id,
                    source_type,
                    pending_transaction_id,
                    transaction_date,
                    description,
                    raw_description,
                    category,
                    transaction_type,
                    transaction_status,
                    amount,
                    is_transfer,
                    is_recurring,
                    needs_review,
                    notes,
                    is_archived
                )
                VALUES (
                    :user_id,
                    :account_id,
                    :external_transaction_id,
                    "plaid",
                    :pending_transaction_id,
                    :transaction_date,
                    :description,
                    :raw_description,
                    :category,
                    :transaction_type,
                    :transaction_status,
                    :amount,
                    :is_transfer,
                    0,
                    :needs_review,
                    NULL,
                    0
                )

                ON DUPLICATE KEY UPDATE

                    account_id =
                        VALUES(account_id),

                    pending_transaction_id =
                        VALUES(pending_transaction_id),

                    transaction_date =
                        VALUES(transaction_date),

                    description =
                        VALUES(description),

                    raw_description =
                        VALUES(raw_description),

                    category =
                        VALUES(category),

                    transaction_type =
                        VALUES(transaction_type),

                    transaction_status =
                        VALUES(transaction_status),

                    amount =
                        VALUES(amount),

                    is_transfer =
                        VALUES(is_transfer),

                    needs_review =
                        VALUES(needs_review),

                    is_archived =
                        0
                '
            );


            $stmt->execute([
                'user_id' =>
                    $userId,

                'account_id' =>
                    $accountId,

                'external_transaction_id' =>
                    $externalTransactionId,

                'pending_transaction_id' =>
                    $pendingTransactionId,

                'transaction_date' =>
                    (string) (
                        $plaidTx['date']
                        ?? date('Y-m-d')
                    ),

                'description' =>
                    $normalized[
                        'description'
                    ],

                'raw_description' =>
                    $rawDescription,

                'category' =>
                    $category,

                'transaction_type' =>
                    $transactionType,

                'transaction_status' =>
                    $status,

                'amount' =>
                    $amount,

                'is_transfer' =>
                    $isTransfer,

                'needs_review' =>
                    $needsReview,
            ]);


            $totalAdded++;
        }


        /*
        |--------------------------------------------------------------------------
        | Modified
        |--------------------------------------------------------------------------
        */

        foreach ($modified as $plaidTx) {

            $externalAccountId = trim(
                (string) (
                    $plaidTx['account_id']
                    ?? ''
                )
            );

            $externalTransactionId = trim(
                (string) (
                    $plaidTx[
                        'transaction_id'
                    ]
                    ?? ''
                )
            );


            if (
                $externalAccountId === '' ||
                $externalTransactionId === ''
            ) {
                $totalSkipped++;
                continue;
            }


            $accountId =
                plaid_local_account_id(
                    $pdo,
                    $userId,
                    $externalAccountId
                );


            if ($accountId === null) {
                $totalSkipped++;
                continue;
            }


            $rawDescription = trim(
                (string) (
                    $plaidTx[
                        'original_description'
                    ]
                    ?? $plaidTx['name']
                    ?? 'Plaid Transaction'
                )
            );


            $fallbackDescription = trim(
                (string) (
                    $plaidTx['merchant_name']
                    ?? $plaidTx['name']
                    ?? $rawDescription
                )
            );


            $providerCategory =
                plaid_transaction_category(
                    $plaidTx
                );


            $normalized =
                normalize_transaction(
                    $pdo,
                    $userId,
                    $rawDescription,
                    $fallbackDescription,
                    $providerCategory
                );


            $amount =
                plaid_signed_amount(
                    $plaidTx
                );


            $classification =
                transaction_classification(
                    $rawDescription,
                    $normalized['category'],
                    $amount
                );


            $transactionType =
                $classification[
                    'transaction_type'
                ];


            $category =
                $classification[
                    'category'
                ];


            $isTransfer =
                (int) $classification[
                    'is_transfer'
                ];


            $needsReview =
                $normalized['matched']
                    ? 0
                    : (int) $classification[
                        'needs_review'
                    ];


            $status =
                !empty(
                    $plaidTx['pending']
                )
                    ? 'pending'
                    : 'posted';


            $pendingTransactionId =
                $plaidTx[
                    'pending_transaction_id'
                ]
                ?? null;


            $stmt = $pdo->prepare(
                '
                UPDATE transactions

                SET
                    account_id =
                        :account_id,

                    transaction_date =
                        :transaction_date,

                    description =
                        :description,

                    raw_description =
                        :raw_description,

                    category =
                        :category,

                    transaction_type =
                        :transaction_type,

                    is_transfer =
                        :is_transfer,

                    transaction_status =
                        :transaction_status,

                    amount =
                        :amount,

                    pending_transaction_id =
                        :pending_transaction_id,

                    needs_review =
                        :needs_review,

                    is_archived =
                        0

                WHERE user_id =
                        :user_id

                  AND source_type =
                        "plaid"

                  AND external_transaction_id =
                        :external_transaction_id
                '
            );


            $stmt->execute([
                'account_id' =>
                    $accountId,

                'transaction_date' =>
                    (string) (
                        $plaidTx['date']
                        ?? date('Y-m-d')
                    ),

                'description' =>
                    $normalized[
                        'description'
                    ],

                'raw_description' =>
                    $rawDescription,

                'category' =>
                    $category,

                'transaction_type' =>
                    $transactionType,

                'is_transfer' =>
                    $isTransfer,

                'transaction_status' =>
                    $status,

                'amount' =>
                    $amount,

                'pending_transaction_id' =>
                    $pendingTransactionId,

                'needs_review' =>
                    $needsReview,

                'user_id' =>
                    $userId,

                'external_transaction_id' =>
                    $externalTransactionId,
            ]);


            $totalModified++;
        }


        /*
        |--------------------------------------------------------------------------
        | Removed
        |--------------------------------------------------------------------------
        */

        foreach ($removed as $plaidTx) {

            $externalTransactionId = trim(
                (string) (
                    $plaidTx[
                        'transaction_id'
                    ]
                    ?? ''
                )
            );


            if ($externalTransactionId === '') {
                $totalSkipped++;
                continue;
            }


            $stmt = $pdo->prepare(
                '
                UPDATE transactions

                SET is_archived = 1

                WHERE user_id =
                        :user_id

                  AND source_type =
                        "plaid"

                  AND external_transaction_id =
                        :external_transaction_id
                '
            );


            $stmt->execute([
                'user_id' =>
                    $userId,

                'external_transaction_id' =>
                    $externalTransactionId,
            ]);


            $totalRemoved++;
        }


        /*
        |--------------------------------------------------------------------------
        | Connection state
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare(
            '
            UPDATE financial_connections

            SET
                sync_cursor =
                    :sync_cursor,

                last_synced_at =
                    NOW(),

                connection_status =
                    "active"

            WHERE id =
                    :connection_id

              AND user_id =
                    :user_id
            '
        );


        $stmt->execute([
            'sync_cursor' =>
                $cursor,

            'connection_id' =>
                $connectionId,

            'user_id' =>
                $userId,
        ]);


        $pdo->commit();

    } catch (Throwable $exception) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo 'ERROR saving connection '
            . $connectionId
            . ': '
            . $exception->getMessage()
            . PHP_EOL;

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Result
|--------------------------------------------------------------------------
*/

echo 'Plaid full sync complete.'
    . PHP_EOL;

echo 'Connections checked: '
    . $totalConnections
    . PHP_EOL;

echo 'Account balances updated: '
    . $totalAccountsUpdated
    . PHP_EOL;

echo 'Transactions added: '
    . $totalAdded
    . PHP_EOL;

echo 'Transactions modified: '
    . $totalModified
    . PHP_EOL;

echo 'Transactions removed: '
    . $totalRemoved
    . PHP_EOL;

echo 'Skipped: '
    . $totalSkipped
    . PHP_EOL;
