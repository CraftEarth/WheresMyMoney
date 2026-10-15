<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$userId = current_user_id();

$billId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$bill = null;
$errorMessage = null;

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

if ($billId > 0) {
    $stmt = db()->prepare(
        '
        SELECT *
        FROM bills
        WHERE id = :id
          AND user_id = :user_id
          AND is_archived = 0
        LIMIT 1
        '
    );

    $stmt->execute([
        'id' => $billId,
        'user_id' => $userId,
    ]);

    $bill = $stmt->fetch();

    if (!$bill) {
        http_response_code(404);
        exit('Bill not found.');
    }
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
        $name = trim(
            (string) ($_POST['bill_name'] ?? '')
        );

        $category = trim(
            (string) ($_POST['category'] ?? '')
        );

        $amount = abs(
            (float) ($_POST['expected_amount'] ?? 0)
        );

        $dueDate = trim(
            (string) ($_POST['next_due_date'] ?? '')
        );

        $frequency = trim(
            (string) ($_POST['frequency'] ?? 'monthly')
        );

        $status = trim(
            (string) ($_POST['bill_status'] ?? 'upcoming')
        );

        $accountId = isset($_POST['account_id'])
            && $_POST['account_id'] !== ''
                ? (int) $_POST['account_id']
                : null;

        $autopay = isset(
            $_POST['autopay']
        ) ? 1 : 0;

        $notes = trim(
            (string) ($_POST['notes'] ?? '')
        );

        if ($name === '') {
            $errorMessage =
                'Bill name is required.';
        }

        if ($amount <= 0) {
            $errorMessage =
                'Expected amount must be greater than zero.';
        }

        if ($dueDate === '') {
            $errorMessage =
                'Next due date is required.';
        }

        if ($category === '') {
            $category = 'Other';
        }

        if ($accountId !== null) {
            $stmt = db()->prepare(
                '
                SELECT id
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

            if (!$stmt->fetch()) {
                $errorMessage =
                    'Choose a valid account.';
            }
        }

        if ($errorMessage === null) {
            if ($billId > 0) {
                $stmt = db()->prepare(
                    '
                    UPDATE bills
                    SET
                        account_id = :account_id,
                        bill_name = :bill_name,
                        category = :category,
                        expected_amount = :expected_amount,
                        next_due_date = :next_due_date,
                        frequency = :frequency,
                        autopay = :autopay,
                        bill_status = :bill_status,
                        notes = :notes
                    WHERE id = :id
                      AND user_id = :user_id
                    '
                );

                $stmt->execute([
                    'account_id' => $accountId,
                    'bill_name' => $name,
                    'category' => $category,
                    'expected_amount' => $amount,
                    'next_due_date' => $dueDate,
                    'frequency' => $frequency,
                    'autopay' => $autopay,
                    'bill_status' => $status,
                    'notes' => $notes !== ''
                        ? $notes
                        : null,
                    'id' => $billId,
                    'user_id' => $userId,
                ]);
            } else {
                $stmt = db()->prepare(
                    '
                    INSERT INTO bills (
                        user_id,
                        account_id,
                        bill_name,
                        category,
                        expected_amount,
                        next_due_date,
                        frequency,
                        autopay,
                        bill_status,
                        notes
                    )
                    VALUES (
                        :user_id,
                        :account_id,
                        :bill_name,
                        :category,
                        :expected_amount,
                        :next_due_date,
                        :frequency,
                        :autopay,
                        :bill_status,
                        :notes
                    )
                    '
                );

                $stmt->execute([
                    'user_id' => $userId,
                    'account_id' => $accountId,
                    'bill_name' => $name,
                    'category' => $category,
                    'expected_amount' => $amount,
                    'next_due_date' => $dueDate,
                    'frequency' => $frequency,
                    'autopay' => $autopay,
                    'bill_status' => $status,
                    'notes' => $notes !== ''
                        ? $notes
                        : null,
                ]);
            }

            header(
                'Location: ' . url('bills.php')
            );

            exit;
        }
    }
}

$pageTitle = $billId > 0
    ? 'Edit Bill'
    : 'Add Bill';

$pageType = 'app';
$activePage = 'bills';

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/bill-form.php';
require_once APP_ROOT . '/includes/footer.php';
