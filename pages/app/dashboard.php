<div class="app-layout">

    <main class="app-main">

        <header class="app-topbar">

            <div>
                <p class="app-greeting">
                    Good morning, Murph
                </p>

                <h1>
                    Here's where your money stands.
                </h1>
            </div>

            <div class="app-topbar-actions">

                <label class="app-search">

                    <input
                        type="search"
                        placeholder="Search transactions..."
                    >

                </label>

                <div class="user-avatar">
                    M
                </div>

            </div>

        </header>


        <section class="summary-grid">

            <article class="summary-card">
                <span>Total Cash</span>

                <strong>
                    <?= money($summary['cash']); ?>
                </strong>

                <small class="positive">
                    +<?= money($summary['income']); ?> this month
                </small>
            </article>


            <article class="summary-card">
                <span>Monthly Income</span>

                <strong>
                    <?= money($summary['income']); ?>
                </strong>

                <small>
                    1 recurring source
                </small>
            </article>


            <article class="summary-card">
                <span>Monthly Spending</span>

                <strong>
                    <?= money($summary['spent']); ?>
                </strong>

                <small>
                    Across tracked categories
                </small>
            </article>


            <article class="summary-card">
                <span>Upcoming Bills</span>

                <strong>
                    <?= money($summary['bills']); ?>
                </strong>

                <small>
                    4 bills tracked
                </small>
            </article>


            <article class="summary-card summary-card-safe">
                <span>Safe to Spend</span>

                <strong>
                    <?= money($summary['safe_to_spend']); ?>
                </strong>

                <small>
                    After bills and obligations
                </small>
            </article>


            <article class="summary-card">
                <span>Net Worth</span>

                <strong>
                    <?= money($summary['net_worth']); ?>
                </strong>

                <small class="positive">
                    Trending upward
                </small>
            </article>

        </section>


        <section class="dashboard-grid dashboard-grid-main">

            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>Your money</small>
                        <h2>Accounts</h2>
                    </div>

                    <a href="<?= e(url('accounts.php')); ?>">
                        View All
                    </a>

                </div>


                <div class="account-list">

                    <?php foreach ($accounts as $account): ?>

                        <div class="account-row">

                            <div class="account-icon">
                                <?= e($account['icon']); ?>
                            </div>

                            <div class="account-copy">

                                <strong>
                                    <?= e($account['name']); ?>
                                </strong>

                                <small>
                                    <?= e($account['type']); ?>

                                    <?php if ($account['last4'] !== ''): ?>
                                        ****<?= e($account['last4']); ?>
                                    <?php endif; ?>
                                </small>

                            </div>

                            <strong>
                                <?= money((float) $account['balance']); ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>


            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>Latest activity</small>
                        <h2>Recent Transactions</h2>
                    </div>

                    <a href="<?= e(url('transactions.php')); ?>">
                        View All
                    </a>

                </div>


                <div class="transaction-list">

                    <?php foreach ($transactions as $transaction): ?>

                        <div class="transaction-row">

                            <div class="transaction-date">
                                <?= e($transaction['date']); ?>
                            </div>

                            <div class="transaction-copy">

                                <strong>
                                    <?= e($transaction['name']); ?>
                                </strong>

                                <small>
                                    <?= e($transaction['category']); ?>
                                </small>

                            </div>

                            <strong
                                class="<?= $transaction['amount'] >= 0
                                    ? 'positive'
                                    : 'negative'; ?>"
                            >
                                <?= $transaction['amount'] >= 0 ? '+' : '-'; ?>
                                <?= money(abs((float) $transaction['amount'])); ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>


            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>What's next</small>
                        <h2>Upcoming Bills</h2>
                    </div>

                    <a href="<?= e(url('bills.php')); ?>">
                        View All
                    </a>

                </div>


                <div class="bill-list">

                    <?php foreach ($bills as $bill): ?>

                        <div class="bill-row">

                            <div>

                                <strong>
                                    <?= e($bill['name']); ?>
                                </strong>

                                <small>
                                    Due <?= e($bill['due']); ?>
                                </small>

                            </div>

                            <div class="bill-meta">

                                <strong>
                                    <?= money((float) $bill['amount']); ?>
                                </strong>

                                <span>
                                    <?= e($bill['status']); ?>
                                </span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>

        </section>


        <section class="dashboard-grid dashboard-grid-bottom">

            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>This month</small>
                        <h2>Where Did My Money Go?</h2>
                    </div>

                </div>


                <div class="spending-list">

                    <?php foreach ($spending as $item): ?>

                        <div class="spending-row">

                            <div class="spending-row-head">

                                <span>
                                    <?= e($item['name']); ?>
                                </span>

                                <strong>
                                    <?= money((float) $item['amount']); ?>
                                </strong>

                            </div>

                            <div class="spending-track">

                                <span
                                    style="width: <?= (int) $item['percent']; ?>%;"
                                ></span>

                            </div>

                            <small>
                                <?= (int) $item['percent']; ?>% of tracked spending
                            </small>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>


            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>Neutral insights</small>
                        <h2>Habit Spending</h2>
                    </div>

                </div>


                <div class="habit-grid">

                    <div>
                        <span>Cannabis</span>
                        <strong>$99.40</strong>
                        <small>This month</small>
                    </div>

                    <div>
                        <span>Alcohol</span>
                        <strong>$74.82</strong>
                        <small>This month</small>
                    </div>

                </div>


                <div class="insight-box">

                    <strong>
                        Spending insight
                    </strong>

                    <p>
                        Habit categories are tracked
                        without judgment.
                    </p>

                </div>

            </article>


            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>Financial position</small>
                        <h2>Money Flow</h2>
                    </div>

                </div>


                <div class="money-flow">

                    <div>
                        <span>Income</span>

                        <strong class="positive">
                            +<?= money($summary['income']); ?>
                        </strong>
                    </div>

                    <div>
                        <span>Spending</span>

                        <strong class="negative">
                            -<?= money($summary['spent']); ?>
                        </strong>
                    </div>

                    <div>
                        <span>Upcoming Bills</span>

                        <strong>
                            <?= money($summary['bills']); ?>
                        </strong>
                    </div>

                    <div class="money-flow-highlight">
                        <span>Safe to Spend</span>

                        <strong>
                            <?= money($summary['safe_to_spend']); ?>
                        </strong>
                    </div>

                </div>

            </article>

        </section>

    </main>

</div>
