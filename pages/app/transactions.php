<div class="app-layout">

    <main class="app-main">

        <header class="transactions-header">

            <div>

                <p class="app-greeting">
                    Money activity
                </p>

                <h1>
                    Transactions
                </h1>

                <p class="transactions-subtitle">
                    Real income, spending, and transfers
                    recorded under your account.
                </p>

            </div>


            <div class="transactions-actions">

                <a
                    class="secondary-button"
                    href="<?= e(
                        url('transaction-rules.php')
                    ); ?>"
                >
                    Rules
                </a>

                <a
                    class="secondary-button"
                    href="<?= e(
                        url('transaction-import.php')
                    ); ?>"
                >
                    Import
                </a>

                <form
                    method="post"
                    action="<?= e(
                        url('apply-transaction-rules.php')
                    ); ?>"
                    class="inline-action-form"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(
                            csrf_token()
                        ); ?>"
                    >

                    <button
                        class="secondary-button"
                        type="submit"
                    >
                        Apply Rules
                    </button>

                </form>

                <a
                    class="primary-button"
                    href="<?= e(
                        url('transaction-form.php')
                    ); ?>"
                >
                    + Add Transaction
                </a>

            </div>

        </header>


        <?php if ($needsReviewCount > 0): ?>

            <div class="review-summary">

                <strong>
                    <?= $needsReviewCount; ?>
                    transaction<?= $needsReviewCount === 1 ? '' : 's'; ?>
                    need review.
                </strong>

                <span>
                    Edit each one to confirm its name and category.
                </span>

            </div>

        <?php endif; ?>


        <?php if ($rulesMessage !== null): ?>

            <div class="settings-message success-message">
                <?= e($rulesMessage); ?>
            </div>

        <?php endif; ?>


        <section class="transaction-summary-grid">

            <article>

                <span>
                    Income
                </span>

                <strong class="positive">
                    +<?= money($totalIncome); ?>
                </strong>

                <small>
                    Excludes transfers
                </small>

            </article>


            <article>

                <span>
                    Spending
                </span>

                <strong class="negative">
                    -<?= money($totalExpenses); ?>
                </strong>

                <small>
                    Excludes transfers
                </small>

            </article>


            <article>

                <span>
                    Transfers
                </span>

                <strong>
                    <?= money($totalTransfers); ?>
                </strong>

                <small>
                    Internal movement
                </small>

            </article>


            <article>

                <span>
                    Transactions
                </span>

                <strong>
                    <?= count($transactions); ?>
                </strong>

                <small>
                    Active records
                </small>

            </article>

        </section>


        <section class="transactions-card">

            <?php if ($transactions === []): ?>

                <div class="accounts-empty">

                    <strong>
                        No transactions yet.
                    </strong>

                    <p>
                        Add your first transaction
                        to begin building real reports.
                    </p>

                    <a
                        class="primary-button"
                        href="<?= e(
                            url('transaction-form.php')
                        ); ?>"
                    >
                        Add Transaction
                    </a>

                </div>

            <?php else: ?>

                <div class="transactions-table">

                    <div class="transactions-table-head">

                        <span>Date</span>
                        <span>Description</span>
                        <span>Category</span>
                        <span>Account</span>
                        <span>Status</span>
                        <span>Amount</span>
                        <span>Actions</span>

                    </div>


                    <?php foreach (
                        $transactions as $transaction
                    ): ?>

                        <article class="transactions-table-row">

                            <div class="transaction-date-cell">

                                <?= e(
                                    date(
                                        'M j',
                                        strtotime(
                                            $transaction[
                                                'transaction_date'
                                            ]
                                        )
                                    )
                                ); ?>

                            </div>


                            <div class="transaction-name-cell">

                                <span class="transaction-icon">

                                    <?= e(
                                        strtoupper(
                                            substr(
                                                $transaction[
                                                    'description'
                                                ],
                                                0,
                                                1
                                            )
                                        )
                                    ); ?>

                                </span>

                                <div>

                                    <strong>
                                        <?= e(
                                            $transaction[
                                                'description'
                                            ]
                                        ); ?>
                                    </strong>

                                    <small>

                                        <?= e(
                                            ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $transaction[
                                                        'transaction_type'
                                                    ]
                                                )
                                            )
                                        ); ?>

                                        <?php if (
                                            (int) $transaction[
                                                'is_recurring'
                                            ] === 1
                                        ): ?>

                                            - Recurring

                                        <?php endif; ?>

                                    </small>

                                </div>

                            </div>


                            <div>
                                <?= e(
                                    $transaction['category']
                                ); ?>
                            </div>


                            <div>
                                <?= e(
                                    $transaction['account_name']
                                ); ?>
                            </div>


                            <div>

                                <span class="status-badge">
                                    <?= e(
                                        ucfirst(
                                            $transaction[
                                                'transaction_status'
                                            ]
                                        )
                                    ); ?>
                                </span>

                            </div>


                            <div
                                class="transaction-amount-cell <?= (
                                    (float) $transaction['amount']
                                    >= 0
                                )
                                    ? 'positive'
                                    : 'negative'; ?>"
                            >

                                <?= (
                                    (float) $transaction['amount']
                                    >= 0
                                )
                                    ? '+'
                                    : '-'; ?>

                                <?= money(
                                    abs(
                                        (float) $transaction[
                                            'amount'
                                        ]
                                    )
                                ); ?>

                            </div>


                            <div class="transaction-actions-cell">

                                <a
                                    href="<?= e(
                                        url(
                                            'transaction-form.php?id='
                                            . $transaction['id']
                                        )
                                    ); ?>"
                                >
                                    Edit
                                </a>


                                <form
                                    method="post"
                                    action="<?= e(
                                        url(
                                            'transaction-archive.php'
                                        )
                                    ); ?>"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(
                                            csrf_token()
                                        ); ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="transaction_id"
                                        value="<?= (int) $transaction[
                                            'id'
                                        ]; ?>"
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





