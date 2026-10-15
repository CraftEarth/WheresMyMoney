<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    header(
        'Location: ' . url('dashboard.php')
    );

    exit;
}

$pageTitle = 'Login';
$pageType = 'public';

$error = null;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(
        (string) ($_POST['email'] ?? '')
    );

    $password = (string) (
        $_POST['password'] ?? ''
    );

    if (
        !verify_csrf(
            $_POST['csrf_token'] ?? null
        )
    ) {
        $error =
            'The form expired. Please try again.';
    } else {
        $stmt = db()->prepare(
            '
            SELECT
                id,
                password_hash
            FROM users
            WHERE email = :email
            LIMIT 1
            '
        );

        $stmt->execute([
            'email' => $email,
        ]);

        $user = $stmt->fetch();

        if (
            !$user ||
            !password_verify(
                $password,
                $user['password_hash']
            )
        ) {
            $error =
                'Email or password is incorrect.';
        } else {
            session_regenerate_id(true);

            $_SESSION['user_id'] =
                (int) $user['id'];

            header(
                'Location: ' . url('dashboard.php')
            );

            exit;
        }
    }
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/pages/auth/login.php';
require_once APP_ROOT . '/includes/footer.php';
