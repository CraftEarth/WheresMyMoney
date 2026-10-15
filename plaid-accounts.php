<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Plaid Accounts';
$pageType = 'app';
$activePage = 'accounts';

$accounts = [];
$errorMessage = null;

$accessToken = $_SESSION[
    'plaid_sandbox_access_token'
] ?? null;

if (!$accessToken) {
    header(
        'Location: ' . url('link-account.php')
    );

    exit;
}

try {
    $response = plaid_request(
        '/accounts/get',
        [
            'access_token' =>
                $accessToken,
        ]
    );

    $accounts =
        $response['accounts']
        ?? [];
} catch (Throwable $exception) {
    $errorMessage =
        $exception->getMessage();
}

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
?>

<div class="app-layout">

    <main class="app-main">

        <header class="transactions-header">

            <div>

                <p class="app-greeting">
                    Plaid connection
                </p>

                <h1>
                    Linked Accounts
                </h1>

            </div>

        </header>


        <?php if ($errorMessage !== null): ?>

            <div class="settings-message error-message">
                <?= e($errorMessage); ?>
            </div>

        <?php endif; ?>


        <section class="import-card">

            <?php if ($accounts === []): ?>

                <div class="dashboard-empty">
                    No Plaid accounts returned.
                </div>

            <?php else: ?>

                <?php foreach ($accounts as $account): ?>

                    <div
                        style="
                            padding: 16px 0;
                            border-bottom:
                                1px solid #edf0f5;
                        "
                    >

                        <strong>
                            <?= e(
                                (string) (
                                    $account['name']
                                    ?? 'Account'
                                )
                            ); ?>
                        </strong>

                        <div
                            style="
                                margin-top: 5px;
                                color: #667085;
                                font-size: 12px;
                            "
                        >
                            Type:
                            <?= e(
                                (string) (
                                    $account['type']
                                    ?? ''
                                )
                            ); ?>

                            &nbsp; | &nbsp;

                            Last 4:
                            <?= e(
                                (string) (
                                    $account['mask']
                                    ?? ''
                                )
                            ); ?>

                            &nbsp; | &nbsp;

                            Current:
                            $<?= number_format(
                                (float) (
                                    $account[
                                        'balances'
                                    ]['current']
                                    ?? 0
                                ),
                                2
                            ); ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </main>

</div>

<?php

require_once APP_ROOT . '/includes/footer.php';
