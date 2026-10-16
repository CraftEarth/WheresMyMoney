<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

header('Content-Type: text/plain');

$userId = current_user_id();
$pdo = db();

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
     * Plaid amounts:
     * positive = money leaving account
     * negative = money entering account
     *
     * Our ledger uses:
     * negative = expense
     * positive = income
     */
    return -1 * (float) (
        $transaction['amount']
        ?? 0
    );
}


function plaid_transaction_type(
    float $signedAmount
): string {
    return $signedAmount >= 0
        ? 'income'
        : 'expense';
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

$totalAdded = 0;
$totalModified = 0;
$totalRemoved = 0;
$totalSkipped = 0;

foreach ($connections as $connection) {
    $connectionId =
        (int) $connection['id'];

    $cursor = $connection[
        'sync_cursor'
    ];

    $accessToken =
        plaid_decrypt_token(
            (string) $connection[
                'access_token_encrypted'
            ]
        );

    $hasMore = true;

    $added = [];
    $modified = [];
    $removed = [];

    while ($hasMore) {
        $payload = [
            'access_token' =>
                $accessToken,

            'count' => 500,

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

        $response = plaid_request(
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


    $pdo->beginTransaction();

    try {
        foreach ($added as $plaidTx) {
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

            $category =
                plaid_transaction_category(
                    $plaidTx
                );

            $normalized =
                normalize_transaction(
                    $pdo,
                    $userId,
                    $rawDescription,
                    $fallbackDescription,
                    $category
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

            $type =
                $classification[
                    'transaction_type'
                ];

            $category =
                $classification[
                    'category'
                ];

            $isTransfer =
                $classification[
                    'is_transfer'
                ];

            $needsReview =
                $normalized['matched']
                    ? 0
                    : $classification[
                        'needs_review'
                    ];

            $status =
                !empty($plaidTx['pending'])
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
                    needs_review =
                        VALUES(needs_review),
                    is_archived = 0
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
                    $classification[
                        'category'
                    ],

                'transaction_type' =>
                    $type,

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


        foreach ($modified as $plaidTx) {
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

            $category =
                plaid_transaction_category(
                    $plaidTx
                );

            $normalized =
                normalize_transaction(
                    $pdo,
                    $userId,
                    $rawDescription,
                    $fallbackDescription,
                    $category
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

            $stmt = $pdo->prepare(
                '
                UPDATE transactions
                SET
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

                    is_archived = 0

                WHERE user_id =
                        :user_id
                  AND source_type =
                        "plaid"
                  AND external_transaction_id =
                        :external_transaction_id
                '
            );

            $stmt->execute([
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
                    $classification[
                        'category'
                    ],

                'transaction_type' =>
                    $classification[
                        'transaction_type'
                    ],

                'is_transfer' =>
                    $classification[
                        'is_transfer'
                    ],

                'transaction_status' =>
                    !empty(
                        $plaidTx['pending']
                    )
                        ? 'pending'
                        : 'posted',

                'amount' =>
                    $amount,

                'pending_transaction_id' =>
                    $plaidTx[
                        'pending_transaction_id'
                    ]
                    ?? null,

                'is_transfer' =>
                    $isTransfer,

                'needs_review' =>
                    $needsReview,

                'user_id' =>
                    $userId,

                'external_transaction_id' =>
                    $externalTransactionId,
            ]);

            $totalModified++;
        }


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
                continue;
            }

            $stmt = $pdo->prepare(
                '
                UPDATE transactions
                SET is_archived = 1
                WHERE user_id = :user_id
                  AND source_type = "plaid"
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
        $pdo->rollBack();

        echo 'ERROR syncing connection '
            . $connectionId
            . ': '
            . $exception->getMessage();

        exit;
    }
}


echo 'Plaid transaction sync complete.'
    . PHP_EOL;

echo 'Added: '
    . $totalAdded
    . PHP_EOL;

echo 'Modified: '
    . $totalModified
    . PHP_EOL;

echo 'Removed: '
    . $totalRemoved
    . PHP_EOL;

echo 'Skipped: '
    . $totalSkipped
    . PHP_EOL;




