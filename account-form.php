<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$userId = current_user_id();

$accountId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$account = null;
$errorMessage = null;

if ($accountId > 0) {
    $stmt = db()->prepare(
        '
        SELECT *
        FROM accounts
        WHERE id = :id
          AND user_id = :user_id
        LIMIT 1
        '
    );

    $stmt->execute([
        'id' => $accountId,
        'user_id' => $userId,
    ]);

    $account = $stmt->fetch();

    if (!$account) {
        http_response_code(404);
        exit('Account not found.');
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
            (string) ($_POST['account_name'] ?? '')
        );

        $institution = trim(
            (string) ($_POST['institution_name'] ?? '')
        );

        $type = trim(
            (string) ($_POST['account_type'] ?? '')
        );

        $last4 = trim(
            (string) ($_POST['last4'] ?? '')
        );

        $balance = (float) (
            $_POST['balance_current'] ?? 0
        );

        $available = (float) (
            $_POST['balance_available'] ?? 0
        );

        $includeCash = isset(
            $_POST['include_in_cash']
        ) ? 1 : 0;

        $includeNetWorth = isset(
            $_POST['include_in_net_worth']
        ) ? 1 : 0;

        $includeSafe = isset(
            $_POST['include_in_safe_to_spend']
        ) ? 1 : 0;

        if ($name === '') {
            $errorMessage =
                'Account name is required.';
        }

        if ($type === '') {
            $errorMessage =
                'Account type is required.';
        }

        if ($errorMessage === null) {
            if ($accountId > 0) {
                $stmt = db()->prepare(
                    '
                    UPDATE accounts
                    SET
                        account_name = :account_name,
                        institution_name = :institution_name,
                        account_type = :account_type,
                        last4 = :last4,
                        balance_current = :balance_current,
                        balance_available = :balance_available,
                        include_in_cash = :include_in_cash,
                        include_in_net_worth = :include_in_net_worth,
                        include_in_safe_to_spend = :include_in_safe_to_spend
                    WHERE id = :id
                      AND user_id = :user_id
                    '
                );

                $stmt->execute([
                    'account_name' => $name,
                    'institution_name' => $institution !== ''
                        ? $institution
                        : null,
                    'account_type' => $type,
                    'last4' => $last4 !== ''
                        ? $last4
                        : null,
                    'balance_current' => $balance,
                    'balance_available' => $available,
                    'include_in_cash' => $includeCash,
                    'include_in_net_worth' => $includeNetWorth,
                    'include_in_safe_to_spend' => $includeSafe,
                    'id' => $accountId,
                    'user_id' => $userId,
                ]);
            } else {
                $stmt = db()->prepare(
                    '
                    INSERT INTO accounts (
                        user_id,
                        account_name,
                        institution_name,
                        account_type,
                        source_type,
                        last4,
                        balance_current,
                        balance_available,
                        include_in_cash,
                        include_in_net_worth,
                        include_in_safe_to_spend
                    )
                    VALUES (
                        :user_id,
                        :account_name,
                        :institution_name,
                        :account_type,
                        "manual",
                        :last4,
                        :balance_current,
                        :balance_available,
                        :include_in_cash,
                        :include_in_net_worth,
                        :include_in_safe_to_spend
                    )
                    '
                );

                $stmt->execute([
                    'user_id' => $userId,
                    'account_name' => $name,
                    'institution_name' => $institution !== ''
                        ? $institution
                        : null,
                    'account_type' => $type,
                    'last4' => $last4 !== ''
                        ? $last4
                        : null,
                    'balance_current' => $balance,
                    'balance_available' => $available,
                    'include_in_cash' => $includeCash,
                    'include_in_net_worth' => $includeNetWorth,
                    'include_in_safe_to_spend' => $includeSafe,
                ]);
            }

            header(
                'Location: ' . url('accounts.php')
            );

            exit;
        }
    }
}

$pageTitle = $accountId > 0
    ? 'Edit Account'
    : 'Add Account';

$pageType = 'app';
$activePage = 'accounts';

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/account-form.php';
require_once APP_ROOT . '/includes/footer.php';
