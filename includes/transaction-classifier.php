<?php

declare(strict_types=1);

function transaction_classification(
    string $description,
    string $category,
    float $amount
): array {
    $text = strtoupper(
        trim($description)
    );

    $categoryText = strtoupper(
        trim($category)
    );

    /*
     * Real interest income.
     */
    if (
        str_contains($text, 'INTRST') ||
        str_contains($text, 'INTEREST') ||
        str_contains($text, 'APY')
    ) {
        return [
            'transaction_type' => 'income',
            'category' => 'Interest Income',
            'is_transfer' => 0,
            'needs_review' => 0,
            'reason' => 'interest',
        ];
    }

    /*
     * Credit card payment variants.
     */
    if (
        preg_match(
            '/\bCREDIT\s+CARD(?:\s+[A-Z0-9*#-]+)*\s+PAYMENT\b/i',
            $description
        ) === 1
    ) {
        return [
            'transaction_type' =>
                $amount >= 0
                    ? 'transfer_in'
                    : 'transfer_out',

            'category' => 'Internal Transfer',
            'is_transfer' => 1,
            'needs_review' => 0,
            'reason' => 'credit_card_payment',
        ];
    }

    /*
     * Other strong transfer descriptions.
     */
    $transferPatterns = [
        'CARD PAYMENT',
        'CC PAYMENT',
        'PAYMENT - THANK',
        'PAYMENT THANK',
        'AUTOMATIC PAYMENT',
        'ACCOUNT TRANSFER',
        'BANK TRANSFER',
        'TRANSFER TO',
        'TRANSFER FROM',
        'ONLINE TRANSFER',
        'MOBILE TRANSFER',
    ];

    foreach ($transferPatterns as $pattern) {
        if (
            str_contains(
                $text,
                $pattern
            )
        ) {
            return [
                'transaction_type' =>
                    $amount >= 0
                        ? 'transfer_in'
                        : 'transfer_out',

                'category' => 'Internal Transfer',
                'is_transfer' => 1,
                'needs_review' => 0,
                'reason' => 'description_transfer',
            ];
        }
    }

    /*
     * Provider transfer category.
     * Interest was handled above first.
     */
    if (
        str_contains(
            $categoryText,
            'TRANSFER'
        )
    ) {
        return [
            'transaction_type' =>
                $amount >= 0
                    ? 'transfer_in'
                    : 'transfer_out',

            'category' => 'Internal Transfer',
            'is_transfer' => 1,
            'needs_review' => 0,
            'reason' => 'provider_transfer',
        ];
    }

    /*
     * Positive transaction in a normal spending
     * category is probably a refund / merchant credit.
     */
    $ordinaryIncomeCategories = [
        'INCOME',
        'PAYROLL',
        'WAGES',
        'SALARY',
        'BENEFITS',
        'INTEREST INCOME',
    ];

    if (
        $amount > 0 &&
        !in_array(
            $categoryText,
            $ordinaryIncomeCategories,
            true
        )
    ) {
        return [
            'transaction_type' => 'refund',
            'category' =>
                $category !== ''
                    ? $category
                    : 'Uncategorized',

            'is_transfer' => 0,
            'needs_review' => 1,
            'reason' => 'possible_refund',
        ];
    }

    return [
        'transaction_type' =>
            $amount >= 0
                ? 'income'
                : 'expense',

        'category' =>
            $category !== ''
                ? $category
                : 'Uncategorized',

        'is_transfer' => 0,
        'needs_review' => 1,
        'reason' => 'unclassified',
    ];
}
