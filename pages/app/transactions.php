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
                    Review income, spending, transfers,
                    and anything that needs attention.
                </p>

            </div>


            <div class="transactions-actions">

                <button
                    class="secondary-button"
                    type="button"
                >
                    Import
                </button>

                <button
                    class="primary-button"
                    type="button"
                >
                    + Add Transaction
                </button>

            </div>

        </header>


        <section class="transaction-summary-grid">

            <article>

                <span>
                    Income
                </span>

                <strong class="positive">
                    +<?= money($totalIncome); ?>
                </strong>

                <small>
                    This period
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
                    Tracked activity
                </small>

            </article>

        </section>


        <section class="transactions-card">

            <div class="transactions-toolbar">

                <div class="transactions-search">

                    <input
                        type="search"
                        placeholder="Search merchant or description..."
                    >

                </div>


                <div class="transactions-filters">

                    <button
                        class="active"
                        type="button"
                    >
                        All
                    </button>

                    <button type="button">
                        Income
                    </button>

                    <button type="button">
                        Spending
                    </button>

                    <button type="button">
                        Transfers
                    </button>

                    <button type="button">
                        Needs Review
                    </button>

                </div>

            </div>


            <div class="transactions-table">

                <div class="transactions-table-head">

                    <span>Date</span>
                    <span>Description</span>
                    <span>Category</span>
                    <span>Account</span>
                    <span>Status</span>
                    <span>Amount</span>

                </div>


                <?php foreach ($transactions as $transaction): ?>

                    <article class="transactions-table-row">

                        <div class="transaction-date-cell">
                            <?= e($transaction['date']); ?>
                        </div>


                        <div class="transaction-name-cell">

                            <span class="transaction-icon">

                                <?= e(
                                    strtoupper(
                                        substr(
                                            $transaction['name'],
                                            0,
                                            1
                                        )
                                    )
                                ); ?>

                            </span>

                            <div>

                                <strong>
                                    <?= e($transaction['name']); ?>
                                </strong>

                                <small>
                                    <?= e($transaction['type']); ?>
                                </small>

                            </div>

                        </div>


                        <div>
                            <?= e($transaction['category']); ?>
                        </div>


                        <div>
                            <?= e($transaction['account']); ?>
                        </div>


                        <div>

                            <span class="status-badge">
                                <?= e($transaction['status']); ?>
                            </span>

                        </div>


                        <div
                            class="transaction-amount-cell <?= $transaction['amount'] >= 0
                                ? 'positive'
                                : 'negative'; ?>"
                        >

                            <?= $transaction['amount'] >= 0
                                ? '+'
                                : '-'; ?>

                            <?= money(
                                abs(
                                    (float) $transaction['amount']
                                )
                            ); ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>

    </main>

</div>
