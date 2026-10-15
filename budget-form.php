<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$userId = current_user_id();

$budgetId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$budget = null;
$errorMessage = null;

if ($budgetId > 0) {
    $stmt = db()->prepare(
        '
        SELECT *
        FROM budgets
        WHERE id = :id
          AND user_id = :user_id
          AND is_archived = 0
        LIMIT 1
        '
    );

    $stmt->execute([
        'id' => $budgetId,
        'user_id' => $userId,
    ]);

    $budget = $stmt->fetch();

    if (!$budget) {
        http_response_code(404);
        exit('Budget not found.');
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
        $category = trim(
            (string) ($_POST['category'] ?? '')
        );

        $monthlyLimit = abs(
            (float) ($_POST['monthly_limit'] ?? 0)
        );

        $monthInput = trim(
            (string) ($_POST['budget_month'] ?? '')
        );

        $budgetMonth = $monthInput !== ''
            ? $monthInput . '-01'
            : '';

        if ($category === '') {
            $errorMessage =
                'Category is required.';
        }

        if ($monthlyLimit <= 0) {
            $errorMessage =
                'Budget amount must be greater than zero.';
        }

        if ($budgetMonth === '') {
            $errorMessage =
                'Budget month is required.';
        }

        if ($errorMessage === null) {
            try {
                if ($budgetId > 0) {
                    $stmt = db()->prepare(
                        '
                        UPDATE budgets
                        SET
                            category = :category,
                            monthly_limit = :monthly_limit,
                            budget_month = :budget_month
                        WHERE id = :id
                          AND user_id = :user_id
                        '
                    );

                    $stmt->execute([
                        'category' => $category,
                        'monthly_limit' => $monthlyLimit,
                        'budget_month' => $budgetMonth,
                        'id' => $budgetId,
                        'user_id' => $userId,
                    ]);
                } else {
                    $stmt = db()->prepare(
                        '
                        INSERT INTO budgets (
                            user_id,
                            category,
                            monthly_limit,
                            budget_month
                        )
                        VALUES (
                            :user_id,
                            :category,
                            :monthly_limit,
                            :budget_month
                        )
                        '
                    );

                    $stmt->execute([
                        'user_id' => $userId,
                        'category' => $category,
                        'monthly_limit' => $monthlyLimit,
                        'budget_month' => $budgetMonth,
                    ]);
                }

                header(
                    'Location: ' . url('budgets.php')
                );

                exit;
            } catch (PDOException $exception) {
                if (
                    $exception->getCode() === '23000'
                ) {
                    $errorMessage =
                        'That category already has a budget for this month.';
                } else {
                    $errorMessage =
                        'Budget could not be saved.';
                }
            }
        }
    }
}

$pageTitle = $budgetId > 0
    ? 'Edit Budget'
    : 'Add Budget';

$pageType = 'app';
$activePage = 'budgets';

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/budget-form.php';
require_once APP_ROOT . '/includes/footer.php';
