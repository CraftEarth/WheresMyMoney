<?php

declare(strict_types=1);

function normalize_transaction(
    PDO $pdo,
    int $userId,
    string $rawDescription,
    string $fallbackDescription,
    string $fallbackCategory = 'Uncategorized'
): array {
    $stmt = $pdo->prepare(
        '
        SELECT
            match_value,
            display_name,
            category
        FROM transaction_rules
        WHERE user_id = :user_id
          AND enabled = 1
          AND is_archived = 0
        ORDER BY
            priority ASC,
            id ASC
        '
    );

    $stmt->execute([
        'user_id' => $userId,
    ]);

    $rules = $stmt->fetchAll();

    foreach ($rules as $rule) {
        $matchValue = trim(
            (string) $rule['match_value']
        );

        if ($matchValue === '') {
            continue;
        }

        if (
            stripos(
                $rawDescription,
                $matchValue
            ) === false
        ) {
            continue;
        }

        return [
            'description' =>
                !empty($rule['display_name'])
                    ? (string) $rule['display_name']
                    : $fallbackDescription,

            'category' =>
                !empty($rule['category'])
                    ? (string) $rule['category']
                    : $fallbackCategory,

            'matched' => true,
            'match_value' => $matchValue,
        ];
    }

    return [
        'description' => $fallbackDescription,
        'category' => $fallbackCategory,
        'matched' => false,
        'match_value' => null,
    ];
}
