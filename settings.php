<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Settings';
$pageType = 'app';
$activePage = 'settings';

$user = current_user();
$userId = current_user_id();

$successMessage = null;
$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !verify_csrf(
            $_POST['csrf_token'] ?? null
        )
    ) {
        $errorMessage =
            'The form expired. Please try again.';
    } else {
        $displayName = trim(
            (string) ($_POST['display_name'] ?? '')
        );

        $currency = (string) (
            $_POST['currency'] ?? 'USD'
        );

        $startingPage = (string) (
            $_POST['starting_page'] ?? 'dashboard'
        );

        if ($displayName === '') {
            $errorMessage =
                'Display name cannot be empty.';
        } else {
            $pdo = db();

            $pdo->beginTransaction();

            try {
                $stmt = $pdo->prepare(
                    '
                    UPDATE users
                    SET display_name = :display_name
                    WHERE id = :user_id
                    '
                );

                $stmt->execute([
                    'display_name' => $displayName,
                    'user_id' => $userId,
                ]);

                $stmt = $pdo->prepare(
                    '
                    UPDATE user_settings
                    SET
                        currency = :currency,
                        starting_page = :starting_page,
                        include_crypto_net_worth = :crypto_net,
                        include_investments_net_worth = :investments_net,
                        include_crypto_safe_to_spend = :crypto_safe,
                        show_habit_spending = :habit,
                        dark_mode = :dark_mode
                    WHERE user_id = :user_id
                    '
                );

                $stmt->execute([
                    'currency' => $currency,
                    'starting_page' => $startingPage,
                    'crypto_net' => isset(
                        $_POST['include_crypto_net_worth']
                    ) ? 1 : 0,
                    'investments_net' => isset(
                        $_POST['include_investments_net_worth']
                    ) ? 1 : 0,
                    'crypto_safe' => isset(
                        $_POST['include_crypto_safe_to_spend']
                    ) ? 1 : 0,
                    'habit' => isset(
                        $_POST['show_habit_spending']
                    ) ? 1 : 0,
                    'dark_mode' => isset(
                        $_POST['dark_mode']
                    ) ? 1 : 0,
                    'user_id' => $userId,
                ]);

                $pdo->commit();

                $successMessage =
                    'Settings saved.';

                $user = current_user();
            } catch (Throwable $exception) {
                $pdo->rollBack();

                $errorMessage =
                    'Settings could not be saved.';
            }
        }
    }
}

$stmt = db()->prepare(
    '
    SELECT *
    FROM user_settings
    WHERE user_id = :user_id
    LIMIT 1
    '
);

$stmt->execute([
    'user_id' => $userId,
]);

$preferences = $stmt->fetch();

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
require_once APP_ROOT . '/pages/app/settings.php';
require_once APP_ROOT . '/includes/footer.php';
