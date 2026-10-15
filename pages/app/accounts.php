<div class="app-layout">

    <main class="app-main">

        <header class="accounts-header">

            <div>

                <p class="app-greeting">
                    Your financial accounts
                </p>

                <h1>
                    Accounts
                </h1>

                <p class="accounts-subtitle">
                    Every place your money lives,
                    moves through, or is tracked.
                </p>

            </div>


            <div class="accounts-actions">

                <a
                    class="secondary-button"
                    href="<?= e(url('account-form.php')); ?>"
                >
                    Add Manual Account
                </a>

                <button
                    class="primary-button"
                    type="button"
                >
                    + Link Account
                </button>

            </div>

        </header>


        <section class="account-summary-grid">

            <article>

                <span>
                    Total Cash
                </span>

                <strong>
                    <?= money($totalCash); ?>
                </strong>

                <small>
                    Accounts included in cash
                </small>

            </article>


            <article>

                <span>
                    Accounts
                </span>

                <strong>
                    <?= count($accounts); ?>
                </strong>

                <small>
                    Active tracked accounts
                </small>

            </article>


            <article>

                <span>
                    Available
                </span>

                <strong>
                    <?= money($totalAvailable); ?>
                </strong>

                <small>
                    Included in safe-to-spend
                </small>

            </article>


            <article>

                <span>
                    Net Worth Position
                </span>

                <strong>
                    <?= money($totalNetWorth); ?>
                </strong>

                <small>
                    Included in net worth
                </small>

            </article>

        </section>


        <section class="accounts-card">

            <div class="accounts-card-header">

                <div>

                    <small>
                        Money sources
                    </small>

                    <h2>
                        Your Accounts
                    </h2>

                </div>

            </div>


            <?php if ($accounts === []): ?>

                <div class="accounts-empty">

                    <strong>
                        No accounts yet.
                    </strong>

                    <p>
                        Add your first manual account
                        to begin tracking real balances.
                    </p>

                    <a
                        class="primary-button"
                        href="<?= e(url('account-form.php')); ?>"
                    >
                        Add Account
                    </a>

                </div>

            <?php else: ?>

                <div class="accounts-table">

                    <div class="accounts-table-head">

                        <span>Account</span>
                        <span>Type</span>
                        <span>Available</span>
                        <span>Balance</span>
                        <span>Actions</span>

                    </div>


                    <?php foreach ($accounts as $account): ?>

                        <article class="accounts-table-row">

                            <div class="account-name-cell">

                                <span class="account-logo">

                                    <?= e(
                                        strtoupper(
                                            substr(
                                                $account['account_name'],
                                                0,
                                                1
                                            )
                                        )
                                    ); ?>

                                </span>

                                <div>

                                    <strong>
                                        <?= e($account['account_name']); ?>
                                    </strong>

                                    <small>

                                        <?= e(
                                            $account['institution_name']
                                            ?? 'Manual Account'
                                        ); ?>

                                        <?php if (
                                            !empty($account['last4'])
                                        ): ?>

                                            ****<?= e($account['last4']); ?>

                                        <?php endif; ?>

                                    </small>

                                </div>

                            </div>


                            <div class="account-type-cell">

                                <strong>
                                    <?= e(
                                        ucfirst(
                                            $account['account_type']
                                        )
                                    ); ?>
                                </strong>

                                <small>
                                    <?= e(
                                        ucfirst(
                                            $account['source_type']
                                        )
                                    ); ?>
                                </small>

                            </div>


                            <div class="account-money-cell">

                                <?= money(
                                    (float) $account[
                                        'balance_available'
                                    ]
                                ); ?>

                            </div>


                            <div class="account-money-cell">

                                <strong>
                                    <?= money(
                                        (float) $account[
                                            'balance_current'
                                        ]
                                    ); ?>
                                </strong>

                            </div>


                            <div class="account-actions-cell">

                                <a
                                    href="<?= e(
                                        url(
                                            'account-form.php?id='
                                            . $account['id']
                                        )
                                    ); ?>"
                                >
                                    Edit
                                </a>


                                <form
                                    method="post"
                                    action="<?= e(
                                        url('account-archive.php')
                                    ); ?>"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(csrf_token()); ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="account_id"
                                        value="<?= (int) $account['id']; ?>"
                                    >

                                    <button type="submit">
                                        Archive
                                    </button>

                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>
