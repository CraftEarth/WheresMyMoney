<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Import Transactions';
$pageType = 'app';
$activePage = 'transactions';

$userId = current_user_id();

$errorMessage = null;
$successMessage = null;
$preview = null;

$stmt = db()->prepare(
    '
    SELECT
        id,
        account_name
    FROM accounts
    WHERE user_id = :user_id
      AND account_status = "active"
    ORDER BY account_name ASC
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$accounts = $stmt->fetchAll();


function detect_csv_delimiter(
    string $line
): string {
    $delimiters = [
        ',' => substr_count($line, ','),
        ';' => substr_count($line, ';'),
        "\t" => substr_count($line, "\t"),
        '|' => substr_count($line, '|'),
    ];

    arsort($delimiters);

    return (string) array_key_first($delimiters);
}


function guess_csv_column(
    array $headers,
    array $names
): ?int {
    foreach ($headers as $index => $header) {
        $normalized = strtolower(
            trim(
                preg_replace(
                    '/[^a-z0-9]+/i',
                    ' ',
                    (string) $header
                )
            )
        );

        foreach ($names as $name) {
            if (
                $normalized === $name ||
                str_contains(
                    $normalized,
                    $name
                )
            ) {
                return (int) $index;
            }
        }
    }

    return null;
}


function parse_money_value(
    string $value
): float {
    $value = trim($value);

    $negativeByParentheses =
        str_starts_with($value, '(') &&
        str_ends_with($value, ')');

    $value = str_replace(
        [
            '$',
            ',',
            '(',
            ')',
            ' ',
        ],
        '',
        $value
    );

    $amount = (float) $value;

    if ($negativeByParentheses) {
        $amount = -abs($amount);
    }

    return $amount;
}


function parse_import_date(
    string $value
): ?string {
    $value = trim($value);

    if ($value === '') {
        return null;
    }

    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return null;
    }

    return date(
        'Y-m-d',
        $timestamp
    );
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
        $action = (string) (
            $_POST['action'] ?? ''
        );

        if ($action === 'preview') {
            $accountId = (int) (
                $_POST['account_id'] ?? 0
            );

            $stmt = db()->prepare(
                '
                SELECT
                    id,
                    account_name
                FROM accounts
                WHERE id = :id
                  AND user_id = :user_id
                  AND account_status = "active"
                LIMIT 1
                '
            );

            $stmt->execute([
                'id' => $accountId,
                'user_id' => $userId,
            ]);

            $account = $stmt->fetch();

            if (!$account) {
                $errorMessage =
                    'Choose a valid account.';
            } elseif (
                !isset($_FILES['csv_file']) ||
                $_FILES['csv_file']['error']
                    !== UPLOAD_ERR_OK
            ) {
                $errorMessage =
                    'Choose a CSV file to upload.';
            } elseif (
                (int) $_FILES['csv_file']['size']
                > 5 * 1024 * 1024
            ) {
                $errorMessage =
                    'CSV file must be smaller than 5 MB.';
            } else {
                $originalName = (string) (
                    $_FILES['csv_file']['name']
                    ?? ''
                );

                $extension = strtolower(
                    pathinfo(
                        $originalName,
                        PATHINFO_EXTENSION
                    )
                );

                if ($extension !== 'csv') {
                    $errorMessage =
                        'Only CSV files are supported.';
                } else {
                    $path = (string) $_FILES[
                        'csv_file'
                    ]['tmp_name'];

                    $handle = fopen(
                        $path,
                        'rb'
                    );

                    if ($handle === false) {
                        $errorMessage =
                            'Could not read the CSV file.';
                    } else {
                        $firstLine = fgets($handle);

                        if ($firstLine === false) {
                            $errorMessage =
                                'CSV file is empty.';
                        } else {
                            $delimiter =
                                detect_csv_delimiter(
                                    $firstLine
                                );

                            rewind($handle);

                            $headers = fgetcsv(
                                $handle,
                                0,
                                $delimiter
                            );

                            if (
                                !is_array($headers) ||
                                $headers === []
                            ) {
                                $errorMessage =
                                    'Could not read CSV headers.';
                            } else {
                                $rows = [];

                                while (
                                    (
                                        $row = fgetcsv(
                                            $handle,
                                            0,
                                            $delimiter
                                        )
                                    ) !== false
                                ) {
                                    if (
                                        count($rows)
                                        >= 5000
                                    ) {
                                        break;
                                    }

                                    if (
                                        count(
                                            array_filter(
                                                $row,
                                                static fn (
                                                    $value
                                                ) =>
                                                    trim(
                                                        (string) $value
                                                    ) !== ''
                                            )
                                        ) === 0
                                    ) {
                                        continue;
                                    }

                                    $rows[] = $row;
                                }

                                $_SESSION[
                                    'csv_import'
                                ] = [
                                    'account_id' =>
                                        $accountId,

                                    'account_name' =>
                                        $account[
                                            'account_name'
                                        ],

                                    'filename' =>
                                        $originalName,

                                    'headers' =>
                                        $headers,

                                    'rows' =>
                                        $rows,
                                ];

                                $preview =
                                    $_SESSION[
                                        'csv_import'
                                    ];
                            }
                        }

                        fclose($handle);
                    }
                }
            }
        }


        if ($action === 'import') {
            $import = $_SESSION[
                'csv_import'
            ] ?? null;

            if (
                !is_array($import) ||
                empty($import['rows']) ||
                empty($import['headers'])
            ) {
                $errorMessage =
                    'Import preview expired. Upload the CSV again.';
            } else {
                $dateColumn = (int) (
                    $_POST['date_column']
                    ?? -1
                );

                $descriptionColumn = (int) (
                    $_POST[
                        'description_column'
                    ]
                    ?? -1
                );

                $amountColumn = (int) (
                    $_POST['amount_column']
                    ?? -1
                );

                $categoryColumn = (int) (
                    $_POST['category_column']
                    ?? -1
                );

                $signMode = (string) (
                    $_POST['sign_mode']
                    ?? 'negative_expense'
                );

                $headers = $import['headers'];
                $rows = $import['rows'];

                if (
                    !isset(
                        $headers[$dateColumn],
                        $headers[
                            $descriptionColumn
                        ],
                        $headers[$amountColumn]
                    )
                ) {
                    $errorMessage =
                        'Map the required CSV columns.';
                } else {
                    $pdo = db();

                    $inserted = 0;
                    $duplicates = 0;
                    $invalid = 0;

                    $duplicateStmt =
                        $pdo->prepare(
                            '
                            SELECT id
                            FROM transactions
                            WHERE user_id = :user_id
                              AND account_id = :account_id
                              AND transaction_date = :transaction_date
                              AND description = :description
                              AND amount = :amount
                              AND is_archived = 0
                            LIMIT 1
                            '
                        );

                    $insertStmt =
                        $pdo->prepare(
                            '
                            INSERT INTO transactions (
                                user_id,
                                account_id,
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
                                notes
                            )
                            VALUES (
                                :user_id,
                                :account_id,
                                :transaction_date,
                                :description,
                                :raw_description,
                                :category,
                                :transaction_type,
                                "posted",
                                :amount,
                                0,
                                0,
                                :needs_review,
                                :notes
                            )
                            '
                        );

                    $pdo->beginTransaction();

                    try {
                        foreach ($rows as $row) {
                            $dateRaw = (string) (
                                $row[$dateColumn]
                                ?? ''
                            );

                            $description = trim(
                                (string) (
                                    $row[
                                        $descriptionColumn
                                    ]
                                    ?? ''
                                )
                            );

                            $amountRaw = (string) (
                                $row[$amountColumn]
                                ?? ''
                            );

                            $date =
                                parse_import_date(
                                    $dateRaw
                                );

                            $amount =
                                parse_money_value(
                                    $amountRaw
                                );

                            if (
                                $date === null ||
                                $description === '' ||
                                abs($amount) < 0.00001
                            ) {
                                $invalid++;
                                continue;
                            }

                            if (
                                $signMode
                                === 'positive_expense'
                            ) {
                                $amount *= -1;
                            }

                            $type = $amount >= 0
                                ? 'income'
                                : 'expense';

                            $category =
                                'Uncategorized';

                            if (
                                $categoryColumn >= 0 &&
                                isset(
                                    $row[
                                        $categoryColumn
                                    ]
                                )
                            ) {
                                $candidateCategory =
                                    trim(
                                        (string) $row[
                                            $categoryColumn
                                        ]
                                    );

                                if (
                                    $candidateCategory
                                    !== ''
                                ) {
                                    $category =
                                        $candidateCategory;
                                }
                            }

                            $normalized =
                                normalize_transaction(
                                    $pdo,
                                    $userId,
                                    $description,
                                    $description,
                                    $category
                                );

                            $cleanDescription =
                                $normalized[
                                    'description'
                                ];

                            $cleanCategory =
                                $normalized[
                                    'category'
                                ];


                            $duplicateStmt->execute([
                                'user_id' =>
                                    $userId,

                                'account_id' =>
                                    (int) $import[
                                        'account_id'
                                    ],

                                'transaction_date' =>
                                    $date,

                                'description' =>
                                    $cleanDescription,

                                'amount' =>
                                    $amount,
                            ]);

                            if (
                                $duplicateStmt->fetch()
                            ) {
                                $duplicates++;
                                continue;
                            }

                            $insertStmt->execute([
                                'user_id' =>
                                    $userId,

                                'account_id' =>
                                    (int) $import[
                                        'account_id'
                                    ],

                                'transaction_date' =>
                                    $date,

                                'description' =>
                                    $description,

                                'raw_description' =>
                                    $description,

                                'category' =>
                                    $category,

                                'transaction_type' =>
                                    $type,

                                'amount' =>
                                    $amount,

                                'needs_review' =>
                                    $normalized['matched']
                                        ? 0
                                        : 1,

                                'notes' =>
                                    'Imported from CSV: '
                                    . $import[
                                        'filename'
                                    ],
                            ]);

                            $inserted++;
                        }

                        $pdo->commit();

                        unset(
                            $_SESSION[
                                'csv_import'
                            ]
                        );

                        $successMessage =
                            $inserted
                            . ' imported, '
                            . $duplicates
                            . ' duplicates skipped, '
                            . $invalid
                            . ' invalid rows skipped.';
                    } catch (Throwable $exception) {
                        $pdo->rollBack();

                        $errorMessage =
                            'Import failed. No rows were added.';
                    }
                }
            }
        }
    }
}

if (
    $preview === null &&
    isset($_SESSION['csv_import']) &&
    is_array(
        $_SESSION['csv_import']
    )
) {
    $preview =
        $_SESSION['csv_import'];
}

$dateGuess = null;
$descriptionGuess = null;
$amountGuess = null;
$categoryGuess = null;

if ($preview !== null) {
    $dateGuess = guess_csv_column(
        $preview['headers'],
        [
            'date',
            'transaction date',
            'posted date',
        ]
    );

    $descriptionGuess =
        guess_csv_column(
            $preview['headers'],
            [
                'description',
                'merchant',
                'name',
                'memo',
                'details',
            ]
        );

    $amountGuess =
        guess_csv_column(
            $preview['headers'],
            [
                'amount',
                'transaction amount',
            ]
        );

    $categoryGuess =
        guess_csv_column(
            $preview['headers'],
            [
                'category',
            ]
        );
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/transaction-import.php';
require_once APP_ROOT . '/includes/footer.php';


