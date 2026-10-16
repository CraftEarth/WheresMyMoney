<div class="app-layout">

    <main class="app-main">

        <header class="app-topbar">

            <div>

                <p class="app-greeting">
                    Welcome back,
                    <?= e(
                        $user['display_name']
                        ?? 'User'
                    ); ?>
                </p>

                <h1>
                    Here's where your money stands.
                </h1>

            </div>


            <div class="app-topbar-actions">

                <label class="app-search">

                    <button
    type="button"
    id="dashboard-plaid-sync"
    class="primary-button"
    style="white-space: nowrap;"
>
    ? Sync Now
</button>

<div
    id="dashboard-sync-status"
    style="
        font-size: 12px;
        color: #667085;
        white-space: nowrap;
    "
></div>
<input
                        type="search"
                        placeholder="Search transactions..."
                    >

                </label>

                <div class="user-avatar">

                    <?= e(
                        strtoupper(
                            substr(
                                $user['display_name']
                                ?? 'U',
                                0,
                                1
                            )
                        )
                    ); ?>

                </div>

            </div>

        </header>


        <section class="summary-grid">

            <article class="summary-card">

                <span>
                    Total Cash
                </span>

                <strong>
                    <?= money($summary['cash']); ?>
                </strong>

                <small>
                    Current account balances
                </small>

            </article>


            <article class="summary-card">

                <span>
                    Monthly Income
                </span>

                <strong>
                    <?= money($summary['income']); ?>
                </strong>

                <small>
                    Posted income this month
                </small>

            </article>


            <article class="summary-card">

                <span>
                    Monthly Spending
                </span>

                <strong>
                    <?= money($summary['spent']); ?>
                </strong>

                <small>
                    Transfers excluded
                </small>

            </article>


            <article class="summary-card">

                <span>
                    Upcoming Bills
                </span>

                <strong>
                    <?= money($summary['bills']); ?>
                </strong>

                <small>
                    Unpaid tracked obligations
                </small>

            </article>


            <article
                class="summary-card summary-card-safe"
            >

                <span>
                    Safe to Spend
                </span>

                <strong>
                    <?= money(
                        $summary['safe_to_spend']
                    ); ?>
                </strong>

                <small>
                    Available cash before upcoming bills
                </small>

            </article>


            <article class="summary-card">

                <span>
                    Net Worth
                </span>

                <strong>
                    <?= money($summary['net_worth']); ?>
                </strong>

                <small>
                    Included tracked accounts
                </small>

            </article>

        
            <section class="money-allocation">

    <div class="money-allocation-heading">

        <div>
            <small>
                YOUR MONEY
            </small>

            <h2>
                Money Allocation
            </h2>
        </div>

        <a href="<?= e(url('reports.php')); ?>">
            View Reports
        </a>

    </div>


    <div class="money-allocation-grid">

        <a
            class="money-allocation-item"
            href="<?= e(url('accounts.php')); ?>"
        >

            <span>
                Cash
            </span>

            <strong>
                <?= money(
                    (float) $summary['cash']
                ); ?>
            </strong>

            <small>
                <?= number_format(
                    (float) $summary[
                        'cash_percent'
                    ],
                    1
                ); ?>% of net worth
            </small>

        </a>


        <a
            class="money-allocation-item"
            href="<?= e(url('crypto.php')); ?>"
        >

            <span>
                Crypto
            </span>

            <strong>
                <?= money(
                    (float) $summary['crypto']
                ); ?>
            </strong>

            <small>
                <?= number_format(
                    (float) $summary[
                        'crypto_percent'
                    ],
                    1
                ); ?>% of net worth
            </small>

        </a>


        <a
            class="money-allocation-item"
            href="<?= e(url('accounts.php')); ?>"
        >

            <span>
                Debt
            </span>

            <strong>
                <?= money(
                    (float) $summary['debt']
                ); ?>
            </strong>

            <small>
                Current liabilities
            </small>

        </a>


        <a
            class="money-allocation-item"
            href="<?= e(url('bills.php')); ?>"
        >

            <span>
                Bills
            </span>

            <strong>
                <?= money(
                    (float) $summary['bills']
                ); ?>
            </strong>

            <small>
                Upcoming obligations
            </small>

        </a>


        <a
            class="money-allocation-item money-allocation-net-worth"
            href="<?= e(url('reports.php')); ?>"
        >

            <span>
                Net Worth
            </span>

            <strong>
                <?= money(
                    (float) $summary[
                        'net_worth'
                    ]
                ); ?>
            </strong>

            <small>
                Assets minus liabilities
            </small>

        </a>

    </div>

</section>
</section>


        <section
            class="dashboard-grid dashboard-grid-main"
        >

            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>Your money</small>
                        <h2>Accounts</h2>
                    </div>

                    <a href="<?= e(
                        url('accounts.php')
                    ); ?>">
                        View All
                    </a>

                </div>


                <?php if ($accounts === []): ?>

                    <div class="dashboard-empty">
                        No accounts yet.
                    </div>

                <?php else: ?>

                    <div class="account-list">

                        <?php foreach (
                            $accounts as $account
                        ): ?>

                            <div class="account-row">

                                <div class="account-icon">

                                    <?= e(
                                        strtoupper(
                                            substr(
                                                $account[
                                                    'account_name'
                                                ],
                                                0,
                                                1
                                            )
                                        )
                                    ); ?>

                                </div>


                                <div class="account-copy">

                                    <strong>
                                        <?= e(
                                            $account[
                                                'account_name'
                                            ]
                                        ); ?>
                                    </strong>

                                    <small>

                                        <?= e(
                                            ucfirst(
                                                $account[
                                                    'account_type'
                                                ]
                                            )
                                        ); ?>

                                        <?php if (
                                            !empty(
                                                $account['last4']
                                            )
                                        ): ?>

                                            ****<?= e(
                                                $account['last4']
                                            ); ?>

                                        <?php endif; ?>

                                    </small>

                                </div>


                                <strong>
                                    <?= money(
                                        (float) $account[
                                            'balance_current'
                                        ]
                                    ); ?>
                                </strong>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </article>


            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>Latest activity</small>
                        <h2>Recent Transactions</h2>
                    </div>

                    <a href="<?= e(
                        url('transactions.php')
                    ); ?>">
                        View All
                    </a>

                </div>


                <?php if (
                    $recentTransactions === []
                ): ?>

                    <div class="dashboard-empty">
                        No transactions yet.
                    </div>

                <?php else: ?>

                    <div class="transaction-list">

                        <?php foreach (
                            $recentTransactions
                            as $transaction
                        ): ?>

                            <div class="transaction-row">

                                <div class="transaction-date">

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


                                <div class="transaction-copy">

                                    <strong>
                                        <?= e(
                                            $transaction[
                                                'description'
                                            ]
                                        ); ?>
                                    </strong>

                                    <small>

                                        <?= e(
                                            $transaction[
                                                'category'
                                            ]
                                        ); ?>

                                        -
                                        <?= e(
                                            $transaction[
                                                'account_name'
                                            ]
                                        ); ?>

                                    </small>

                                </div>


                                <strong
                                    class="<?= (
                                        (float) $transaction[
                                            'amount'
                                        ] >= 0
                                    )
                                        ? 'positive'
                                        : 'negative'; ?>"
                                >

                                    <?= (
                                        (float) $transaction[
                                            'amount'
                                        ] >= 0
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

                                </strong>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </article>


            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>What's next</small>
                        <h2>Upcoming Bills</h2>
                    </div>

                    <a href="<?= e(
                        url('bills.php')
                    ); ?>">
                        View All
                    </a>

                </div>


                <?php if ($dashboardBills === []): ?>

                    <div class="dashboard-empty">
                        No upcoming bills.
                    </div>

                <?php else: ?>

                    <div class="bill-list">

                        <?php foreach (
                            $dashboardBills as $bill
                        ): ?>

                            <div class="bill-row">

                                <div>

                                    <strong>
                                        <?= e(
                                            $bill['bill_name']
                                        ); ?>
                                    </strong>

                                    <small>
                                        Due
                                        <?= e(
                                            date(
                                                'M j',
                                                strtotime(
                                                    $bill[
                                                        'next_due_date'
                                                    ]
                                                )
                                            )
                                        ); ?>
                                    </small>

                                </div>

                                <div class="bill-meta">

                                    <strong>
                                        <?= money(
                                            (float) $bill[
                                                'expected_amount'
                                            ]
                                        ); ?>
                                    </strong>

                                    <span>
                                        <?= e(
                                            ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $bill[
                                                        'bill_status'
                                                    ]
                                                )
                                            )
                                        ); ?>
                                    </span>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </article>

        </section>


        <section
            class="dashboard-grid dashboard-grid-bottom"
        >

            <article class="dashboard-card">

                <div class="card-heading">

                    <div>
                        <small>This month</small>
                        <h2>Gross Spending by Category</h2>
                    </div>

                </div>


                <?php if ($spending === []): ?>

                    <div class="dashboard-empty">
                        No expense data this month.
                    </div>

                <?php else: ?>

                    <div class="spending-list">

                        <?php foreach (
                            $spending as $item
                        ): ?>

                            <div class="spending-row">

                                <div class="spending-row-head">

                                    <span>
                                        <?= e(
                                            $item['name']
                                        ); ?>
                                    </span>

                                    <strong>
                                        <?= money(
                                            (float) $item[
                                                'amount'
                                            ]
                                        ); ?>
                                    </strong>

                                </div>


                                <div class="spending-track">

                                    <span
                                        style="width: <?= min(
                                            100,
                                            (int) $item[
                                                'percent'
                                            ]
                                        ); ?>%;"
                                    ></span>

                                </div>


                                <small>
                                    <?= (int) $item[
                                        'percent'
                                    ]; ?>%
                                    of tracked spending
                                </small>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

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

                        <span>
                            Cannabis
                        </span>

                        <strong>
                            <?= money(
                                $habitSpending[
                                    'Cannabis'
                                ]
                            ); ?>
                        </strong>

                        <small>
                            This month
                        </small>

                    </div>


                    <div>

                        <span>
                            Alcohol
                        </span>

                        <strong>
                            <?= money(
                                $habitSpending[
                                    'Alcohol'
                                ]
                            ); ?>
                        </strong>

                        <small>
                            This month
                        </small>

                    </div>

                </div>


                <div class="insight-box">

                    <strong>
                        Spending insight
                    </strong>

                    <p>
                        Categories are tracked without judgment
                        and reflect only the transactions
                        you've recorded.
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

                        <span>
                            Income
                        </span>

                        <strong class="positive">
                            +<?= money(
                                $summary['income']
                            ); ?>
                        </strong>

                    </div>


                    <div>

                        <span>
                            Spending
                        </span>

                        <strong class="negative">
                            -<?= money(
                                $summary['spent']
                            ); ?>
                        </strong>

                    </div>


                    <div>

                        <span>
                            Upcoming Bills
                        </span>

                        <strong>
                            <?= money(
                                $summary['bills']
                            ); ?>
                        </strong>

                    </div>


                    <div class="money-flow-highlight">

                        <span>
                            Safe to Spend
                        </span>

                        <strong>
                            <?= money(
                                $summary[
                                    'safe_to_spend'
                                ]
                            ); ?>
                        </strong>

                    </div>

                </div>

            </article>

        </section>

    </main>

</div>




<script>
(function () {
    const dashboardPlaidSync =
        document.getElementById(
            'dashboard-plaid-sync'
        );

    const syncStatus =
        document.getElementById(
            'dashboard-sync-status'
        );

    if (!dashboardPlaidSync) {
        return;
    }

    dashboardPlaidSync.addEventListener(
        'click',
        async function () {
            dashboardPlaidSync.disabled = true;
            dashboardPlaidSync.textContent =
                'Syncing...';

            if (syncStatus) {
                syncStatus.textContent =
                    'Checking Plaid for updates...';
            }

            try {
                const response = await fetch(
                    '<?= e(url('plaid-sync.php')); ?>',
                    {
                        credentials: 'same-origin',
                        cache: 'no-store'
                    }
                );

                const result =
                    await response.text();

                if (!response.ok) {
                    throw new Error(
                        result ||
                        'Plaid sync failed.'
                    );
                }

                if (
                    result.toLowerCase()
                        .includes('error')
                ) {
                    throw new Error(result);
                }

                dashboardPlaidSync.textContent =
                    '? Synced';

                if (syncStatus) {
                    syncStatus.textContent =
                        'Sync complete. Refreshing...';
                }

                setTimeout(
                    function () {
                        window.location.reload();
                    },
                    700
                );

            } catch (error) {
                dashboardPlaidSync.disabled =
                    false;

                dashboardPlaidSync.textContent =
                    '? Sync Now';

                if (syncStatus) {
                    syncStatus.textContent =
                        'Sync failed: '
                        + error.message;
                }
            }
        }
    );
})();
</script>
<!-- BEGIN DASHBOARD STATUS COLORS -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    function parseMoney(text) {

        if (!text) {
            return 0;
        }

        const cleaned = text
            .replace(/[$,%+]/g, '')
            .replace(/,/g, '')
            .trim();

        const value =
            parseFloat(cleaned);

        return Number.isFinite(value)
            ? value
            : 0;
    }


    /*
     * Find the six top dashboard cards by their visible labels.
     */
    const topMetrics = {};

    document
        .querySelectorAll('article')
        .forEach(function (card) {

            const text =
                card.innerText.trim();

            [
                'Total Cash',
                'Monthly Income',
                'Monthly Spending',
                'Upcoming Bills',
                'Safe to Spend',
                'Net Worth'
            ].forEach(function (label) {

                if (
                    text.startsWith(label)
                ) {
                    topMetrics[label] =
                        card;
                }
            });
        });


    function cardValue(label) {

        const card =
            topMetrics[label];

        if (!card) {
            return 0;
        }

        const strong =
            card.querySelector('strong');

        return parseMoney(
            strong
                ? strong.textContent
                : ''
        );
    }


    const cash =
        cardValue('Total Cash');

    const income =
        cardValue('Monthly Income');

    const spending =
        cardValue('Monthly Spending');

    const bills =
        cardValue('Upcoming Bills');

    const safe =
        cardValue('Safe to Spend');

    const netWorth =
        cardValue('Net Worth');


    function setState(
        element,
        state
    ) {

        if (!element) {
            return;
        }

        element.classList.remove(
            'money-state-positive',
            'money-state-negative',
            'money-state-neutral'
        );

        element.classList.add(
            'money-state-' + state
        );
    }


    /*
     * ========================================================
     * TOP DASHBOARD RULES
     * ========================================================
     *
     * Cash:
     * green when current cash can cover upcoming bills.
     *
     * Income:
     * green when income keeps pace with spending.
     *
     * Spending:
     * green when spending <= income.
     *
     * Bills:
     * green when current cash covers them.
     *
     * Safe to Spend:
     * green only when something remains after obligations.
     *
     * Net Worth:
     * green when positive.
     */


    setState(
        topMetrics['Total Cash'],
        cash >= bills
            ? 'positive'
            : 'negative'
    );


    setState(
        topMetrics['Monthly Income'],
        income >= spending
            ? 'positive'
            : 'negative'
    );


    setState(
        topMetrics['Monthly Spending'],
        spending <= income
            ? 'positive'
            : 'negative'
    );


    setState(
        topMetrics['Upcoming Bills'],
        bills === 0 || cash >= bills
            ? 'positive'
            : 'negative'
    );


    setState(
        topMetrics['Safe to Spend'],
        safe > 0
            ? 'positive'
            : 'negative'
    );


    setState(
        topMetrics['Net Worth'],
        netWorth > 0
            ? 'positive'
            : 'negative'
    );


    /*
     * ========================================================
     * MONEY ALLOCATION
     * ========================================================
     */

    document
        .querySelectorAll(
            '.money-allocation-item'
        )
        .forEach(function (item) {

            const labelElement =
                item.querySelector('span');

            const valueElement =
                item.querySelector('strong');

            if (
                !labelElement ||
                !valueElement
            ) {
                return;
            }

            const label =
                labelElement
                    .textContent
                    .trim();

            const value =
                parseMoney(
                    valueElement.textContent
                );


            if (label === 'Cash') {

                setState(
                    item,
                    cash >= bills
                        ? 'positive'
                        : 'negative'
                );

                return;
            }


            if (label === 'Crypto') {

                setState(
                    item,
                    value > 0
                        ? 'positive'
                        : 'neutral'
                );

                return;
            }


            if (label === 'Debt') {

                setState(
                    item,
                    value <= 0
                        ? 'positive'
                        : 'negative'
                );

                return;
            }


            if (label === 'Bills') {

                setState(
                    item,
                    value === 0 ||
                    cash >= value
                        ? 'positive'
                        : 'negative'
                );

                return;
            }


            if (label === 'Net Worth') {

                setState(
                    item,
                    value > 0
                        ? 'positive'
                        : 'negative'
                );
            }
        });

});
</script>

<!-- END DASHBOARD STATUS COLORS -->
