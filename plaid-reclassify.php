<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

header('Content-Type: text/plain');

$pdo = db();
$userId = current_user_id();

$stmt = $pdo->prepare(
    '
    SELECT
        id,
        description,
        raw_description,
        category,
        amount
    FROM transactions
    WHERE user_id = :user_id
      AND source_type = "plaid"
      AND is_archived = 0
    ORDER BY id ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$rows = $stmt->fetchAll();

$update = $pdo->prepare(
    '
    UPDATE transactions
    SET
        transaction_type = :transaction_type,
        category = :category,
        is_transfer = :is_transfer,
        needs_review = :needs_review
    WHERE id = :id
      AND user_id = :user_id
    '
);

$pdo->beginTransaction();

try {
    $transfers = 0;
    $refunds = 0;
    $interest = 0;

    foreach ($rows as $row) {
        $description = trim(
            (string) (
                $row['raw_description']
                ?: $row['description']
            )
        );

        $category = (string) $row['category'];

        if (
            str_starts_with(
                $category,
                'Refund - '
            )
        ) {
            $category = substr(
                $category,
                strlen('Refund - ')
            );
        }

        if ($category === 'Internal Transfer') {
            $category = 'Transfer';
        }

        $classification =
            transaction_classification(
                $description,
                $category,
                (float) $row['amount']
            );

        $update->execute([
            'transaction_type' =>
                $classification['transaction_type'],

            'category' =>
                $classification['category'],

            'is_transfer' =>
                $classification['is_transfer'],

            'needs_review' =>
                $classification['needs_review'],

            'id' =>
                (int) $row['id'],

            'user_id' =>
                $userId,
        ]);

        if ($classification['is_transfer']) {
            $transfers++;
        }

        if (
            $classification['transaction_type']
            === 'refund'
        ) {
            $refunds++;
        }

        if (
            $classification['reason']
            === 'interest'
        ) {
            $interest++;
        }
    }

    $pdo->commit();

    echo "Reclassification complete.\n";
    echo "Transfers: {$transfers}\n";
    echo "Refunds: {$refunds}\n";
    echo "Interest income: {$interest}\n";

} catch (Throwable $exception) {
    $pdo->rollBack();

    echo 'ERROR: '
        . $exception->getMessage();
}
