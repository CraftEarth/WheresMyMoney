<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    header(
        'Location: ' . url('dashboard.php')
    );

    exit;
}

$pageTitle = 'Create Account';
$pageType = 'public';

$errors = [];

$email = '';
$displayName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(
        (string) ($_POST['email'] ?? '')
    );

    $displayName = trim(
        (string) ($_POST['display_name'] ?? '')
    );

    $password = (string) (
        $_POST['password'] ?? ''
    );

    $confirmPassword = (string) (
        $_POST['confirm_password'] ?? ''
    );

    if (
        !verify_csrf(
            $_POST['csrf_token'] ?? null
        )
    ) {
        $errors[] = 'The form expired. Please try again.';
    }

    if (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {
        $errors[] = 'Enter a valid email address.';
    }

    if ($displayName === '') {
        $errors[] = 'Enter your name.';
    }

    if (strlen($password) < 8) {
        $errors[] =
            'Password must be at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if ($errors === []) {
        $stmt = db()->prepare(
            '
            SELECT id
            FROM users
            WHERE email = :email
            LIMIT 1
            '
        );

        $stmt->execute([
            'email' => $email,
        ]);

        if ($stmt->fetch()) {
            $errors[] =
                'An account with that email already exists.';
        }
    }

    if ($errors === []) {
        $pdo = db();

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                '
                INSERT INTO users (
                    email,
                    password_hash,
                    display_name
                )
                VALUES (
                    :email,
                    :password_hash,
                    :display_name
                )
                '
            );

            $stmt->execute([
                'email' => $email,
                'password_hash' => password_hash(
                    $password,
                    PASSWORD_DEFAULT
                ),
                'display_name' => $displayName,
            ]);

            $userId = (int) $pdo->lastInsertId();

            $stmt = $pdo->prepare(
                '
                INSERT INTO user_settings (
                    user_id
                )
                VALUES (
                    :user_id
                )
                '
            );

            $stmt->execute([
                'user_id' => $userId,
            ]);

            $pdo->commit();

            session_regenerate_id(true);

            $_SESSION['user_id'] = $userId;

            header(
                'Location: ' . url('dashboard.php')
            );

            exit;
        } catch (Throwable $exception) {
            $pdo->rollBack();

            $errors[] =
                'Account could not be created.';
        }
    }
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/pages/auth/register.php';
require_once APP_ROOT . '/includes/footer.php';
