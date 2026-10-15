<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Transaction Rules';
$pageType = 'app';
$activePage = 'transactions';

$userId = current_user_id();

$ruleId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$rule = null;
$errorMessage = null;
$successMessage = null;

if ($ruleId > 0) {
    $stmt = db()->prepare(
        '
        SELECT *
        FROM transaction_rules
        WHERE id = :id
          AND user_id = :user_id
          AND is_archived = 0
        LIMIT 1
        '
    );

    $stmt->execute([
        'id' => $ruleId,
        'user_id' => $userId,
    ]);

    $rule = $stmt->fetch();

    if (!$rule) {
        http_response_code(404);
        exit('Rule not found.');
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
        $postedRuleId = (int) (
            $_POST['rule_id'] ?? 0
        );

        $matchValue = trim(
            (string) ($_POST['match_value'] ?? '')
        );

        $displayName = trim(
            (string) ($_POST['display_name'] ?? '')
        );

        $category = trim(
            (string) ($_POST['category'] ?? '')
        );

        $priority = max(
            1,
            (int) ($_POST['priority'] ?? 100)
        );

        $enabled = isset(
            $_POST['enabled']
        ) ? 1 : 0;

        if ($matchValue === '') {
            $errorMessage =
                'Match text is required.';
        }

        if ($errorMessage === null) {
            if ($postedRuleId > 0) {
                $stmt = db()->prepare(
                    '
                    UPDATE transaction_rules
                    SET
                        match_value = :match_value,
                        display_name = :display_name,
                        category = :category,
                        priority = :priority,
                        enabled = :enabled
                    WHERE id = :id
                      AND user_id = :user_id
                    '
                );

                $stmt->execute([
                    'match_value' => $matchValue,
                    'display_name' => $displayName !== ''
                        ? $displayName
                        : null,
                    'category' => $category !== ''
                        ? $category
                        : null,
                    'priority' => $priority,
                    'enabled' => $enabled,
                    'id' => $postedRuleId,
                    'user_id' => $userId,
                ]);

                $successMessage =
                    'Rule updated.';
            } else {
                $stmt = db()->prepare(
                    '
                    INSERT INTO transaction_rules (
                        user_id,
                        match_value,
                        display_name,
                        category,
                        priority,
                        enabled
                    )
                    VALUES (
                        :user_id,
                        :match_value,
                        :display_name,
                        :category,
                        :priority,
                        :enabled
                    )
                    '
                );

                $stmt->execute([
                    'user_id' => $userId,
                    'match_value' => $matchValue,
                    'display_name' => $displayName !== ''
                        ? $displayName
                        : null,
                    'category' => $category !== ''
                        ? $category
                        : null,
                    'priority' => $priority,
                    'enabled' => $enabled,
                ]);

                $successMessage =
                    'Rule created.';
            }

            $ruleId = 0;
            $rule = null;
        }
    }
}

$stmt = db()->prepare(
    '
    SELECT *
    FROM transaction_rules
    WHERE user_id = :user_id
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

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/transaction-rules.php';
require_once APP_ROOT . '/includes/footer.php';
