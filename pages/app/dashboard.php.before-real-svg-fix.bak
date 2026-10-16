<?php

function dashboard_icon(
    string $name,
    string $class = ''
): string {

    $icons = [

        'wallet' => '
            <path d="M4 7h15a2 2 0 0 1 2 2v10H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h12"/>
            <path d="M16 11h5v5h-5a2.5 2.5 0 0 1 0-5Z"/>
        ',

        'income' => '
            <path d="M12 20V5"/>
            <path d="m6 11 6-6 6 6"/>
        ',

        'expense' => '
            <path d="M12 4v15"/>
            <path d="m18 13-6 6-6-6"/>
        ',

        'calendar' => '
            <rect x="3" y="5" width="18" height="16" rx="2"/>
            <path d="M16 3v4M8 3v4M3 10h18"/>
        ',

        'shield-check' => '
            <path d="M12 3 20 6v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/>
            <path d="m8.5 12 2.2 2.2 4.8-5"/>
        ',

        'trend' => '
            <path d="m4 17 5-5 4 3 7-8"/>
            <path d="M15 7h5v5"/>
        ',

        'pie' => '
            <path d="M11 3a9 9 0 1 0 9 9h-9z"/>
            <path d="M14 3.5V9h5.5A8 8 0 0 0 14 3.5Z"/>
        ',

        'bitcoin' => '
            <path d="M9 3v18M13 3v18"/>
            <path d="M7 6h7a3 3 0 0 1 0 6H7"/>
            <path d="M7 12h8a3 3 0 0 1 0 6H7"/>
        ',

        'card' => '
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="M3 10h18M7 15h4"/>
        ',

        'receipt' => '
            <path d="M6 3h12v18l-2-1.4L14 21l-2-1.4L10 21l-2-1.4L6 21z"/>
            <path d="M9 8h6M9 12h6M9 16h4"/>
        ',

        'transfer' => '
            <path d="M4 8h13"/>
            <path d="m14 5 3 3-3 3"/>
            <path d="M20 16H7"/>
            <path d="m10 13-3 3 3 3"/>
        ',

        'person' => '
            <circle cx="12" cy="8" r="3"/>
            <path d="M5 20c.7-4 3-6 7-6s6.3 2 7 6"/>
        ',

        'fuel' => '
            <path d="M5 21V4h10v17"/>
            <path d="M4 21h12"/>
            <path d="M8 8h4"/>
            <path d="M15 7h2l3 3v7a2 2 0 0 1-4 0v-4"/>
        ',

        'chart' => '
            <path d="M5 20V11M12 20V5M19 20v-8"/>
        ',

        'spark' => '
            <path d="m12 3 1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5z"/>
            <path d="m19 15 .8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/>
        ',

        'sync' => '
            <path d="M20 7v5h-5"/>
            <path d="M4 17v-5h5"/>
            <path d="M6.5 8a7 7 0 0 1 11.7-2L20 8"/>
            <path d="M17.5 16a7 7 0 0 1-11.7 2L4 16"/>
        ',

    ];

    $body = $icons[$name] ?? '';

    return '
        <svg
            class="wm-svg-icon ' . e($class) . '"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            ' . $body . '
        </svg>
    ';
}
?>
<?php

$positiveTransactions = array_values(
    array_filter(
        $recentTransactions,
        static function (array $transaction): bool {
            return (float) ($transaction['amount'] ?? 0) > 0;
        }
    )
);

$positiveTransactions = array_slice(
    $positiveTransactions,
    0,
    3
);

$positiveTotal = 0.00;

foreach ($positiveTransactions as $transaction) {
    $positiveTotal += (float) $transaction['amount'];
}

$displayName = trim(
    (string) (
        $user['display_name']
        ?? 'User'
    )
);

$firstName = explode(
    ' ',
    $displayName
)[0] ?? $displayName;

$initial = strtoupper(
    substr(
        $displayName,
        0,
        1
    )
);
?>

<div class="app-layout dashboard-v2">

    <main class="app-main wm-dashboard">

        <header class="wm-topbar">

            <div class="wm-topbar-copy">

                <h1>
                    Good evening,
                    <?= e($firstName); ?>
                </h1>

                <p>
                    Here's where your money stands today.
                </p>

            </div>


            <div class="wm-topbar-actions">

                <div class="wm-date">
                    <span>?</span>

                    <?= e(
                        date(
                            'l, M j, Y'
                        )
                    ); ?>
                </div>


                <button
                    type="button"
                    id="dashboard-plaid-sync"
                    class="wm-button wm-button-primary"
                >
                    ? Sync Now
                </button>


                <a
                    href="<?= e(
                        url('link-account.php')
                    ); ?>"
                    class="wm-button wm-button-light"
                >
                    + Add Account
                </a>


                <div class="wm-user-avatar">
                    <?= e($initial); ?>
                </div>

            </div>

        </header>


        <section class="wm-summary-grid">

            <article
                class="wm-summary-card <?= ((float) $summary['cash'] >= (float) $summary['bills']) ? 'wm-state-success' : 'wm-state-danger'; ?>"
                data-dashboard-metric="cash"
            >

                <div class="wm-summary-head">
                    <span class="wm-summary-icon">?</span>
                    <span>Total Cash</span>
                </div>

                <strong>
                    <?= money(
                        (float) $summary['cash']
                    ); ?>
                </strong>

                <small>
                    Available across cash accounts
                </small>

                <div class="wm-mini-line wm-line-red"></div>

            </article>


            <article
                class="wm-summary-card <?= ((float) $summary['income'] > 0) ? 'wm-state-success' : 'wm-state-danger'; ?>"
                data-dashboard-metric="income"
            >

                <div class="wm-summary-head">
                    <span class="wm-summary-icon">?</span>
                    <span>Money In This Month</span>
                </div>

                <strong>
                    <?= money(
                        (float) $summary['income']
                    ); ?>
                </strong>

                <small>
                    Posted incoming money
                </small>

                <div class="wm-mini-line wm-line-green"></div>

            </article>


            <article
                class="wm-summary-card <?= ((float) $summary['spent'] <= (float) $summary['income']) ? 'wm-state-success' : 'wm-state-danger'; ?>"
                data-dashboard-metric="spending"
            >

                <div class="wm-summary-head">
                    <span class="wm-summary-icon">?</span>
                    <span>Money Out This Month</span>
                </div>

                <strong>
                    <?= money(
                        (float) $summary['spent']
                    ); ?>
                </strong>

                <small>
                    Transfers excluded
                </small>

                <div class="wm-mini-line wm-line-red"></div>

            </article>


            <article
                class="wm-summary-card <?= ((float) $summary['cash'] >= (float) $summary['bills']) ? 'wm-state-success' : 'wm-state-warning'; ?>"
                data-dashboard-metric="bills"
            >

                <div class="wm-summary-head">
                    <span class="wm-summary-icon">?</span>
                    <span>Upcoming Bills</span>
                </div>

                <strong>
                    <?= money(
                        (float) $summary['bills']
                    ); ?>
                </strong>

                <small>
                    Known unpaid obligations
                </small>

            </article>


            <article
                class="wm-summary-card <?= ((float) $summary['safe_to_spend'] > 0) ? 'wm-state-success' : 'wm-state-danger'; ?>"
                data-dashboard-metric="safe"
            >

                <div class="wm-summary-head">
                    <span class="wm-summary-icon">?</span>
                    <span>Safe to Spend</span>
                </div>

                <strong>
                    <?= money(
                        (float) $summary[
                            'safe_to_spend'
                        ]
                    ); ?>
                </strong>

                <small>
                    After upcoming bills
                </small>

            </article>


            <article
                class="wm-summary-card <?= ((float) $summary['net_worth'] > 0) ? 'wm-state-success' : 'wm-state-danger'; ?>"
                data-dashboard-metric="net-worth"
            >

                <div class="wm-summary-head">
                    <span class="wm-summary-icon">?</span>
                    <span>Net Worth</span>
                </div>

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

                <div class="wm-mini-line wm-line-green"></div>

            </article>

        </section>


        <section class="wm-allocation">

            <div class="wm-section-heading">

                <div>
                    <h2>
                        ? Money Allocation
                    </h2>

                    <p>
                        How your money is distributed
                    </p>
                </div>

                <a href="<?= e(
                    url('reports.php')
                ); ?>">
                    View Detailed Report ?
                </a>

            </div>


            <div class="wm-allocation-grid">

                <a
                    href="<?= e(
                        url('accounts.php')
                    ); ?>"
                    class="wm-allocation-cell wm-allocation-cash"
                >

                    <span class="wm-allocation-icon">
                        $
                    </span>

                    <div>
                        <small>Cash</small>

                        <strong>
                            <?= money(
                                (float) $summary[
                                    'cash'
                                ]
                            ); ?>
                        </strong>

                        <p>
                            <?= number_format(
                                (float) $summary[
                                    'cash_percent'
                                ],
                                1
                            ); ?>% of net worth
                        </p>
                    </div>

                </a>


                <a
                    href="<?= e(
                        url('crypto.php')
                    ); ?>"
                    class="wm-allocation-cell wm-allocation-crypto"
                >

                    <span class="wm-allocation-icon">
                        ?
                    </span>

                    <div>
                        <small>Crypto</small>

                        <strong>
                            <?= money(
                                (float) $summary[
                                    'crypto'
                                ]
                            ); ?>
                        </strong>

                        <p>
                            <?= number_format(
                                (float) $summary[
                                    'crypto_percent'
                                ],
                                1
                            ); ?>% of net worth
                        </p>
                    </div>

                </a>


                <a
                    href="<?= e(
                        url('accounts.php')
                    ); ?>"
                    class="wm-allocation-cell wm-allocation-debt"
                >

                    <span class="wm-allocation-icon">
                        -
                    </span>

                    <div>
                        <small>Debt</small>

                        <strong>
                            <?= money(
                                (float) $summary[
                                    'debt'
                                ]
                            ); ?>
                        </strong>

                        <p>
                            Current liabilities
                        </p>
                    </div>

                </a>


                <a
                    href="<?= e(
                        url('bills.php')
                    ); ?>"
                    class="wm-allocation-cell wm-allocation-bills"
                >

                    <span class="wm-allocation-icon">
                        ?
                    </span>

                    <div>
                        <small>Bills</small>

                        <strong>
                            <?= money(
                                (float) $summary[
                                    'bills'
                                ]
                            ); ?>
                        </strong>

                        <p>
                            Upcoming obligations
                        </p>
                    </div>

                </a>


                <a
                    href="<?= e(
                        url('reports.php')
                    ); ?>"
                    class="wm-allocation-cell wm-allocation-worth"
                >

                    <span class="wm-allocation-icon">
                        ?
                    </span>

                    <div>
                        <small>Net Worth</small>

                        <strong>
                            <?= money(
                                (float) $summary[
                                    'net_worth'
                                ]
                            ); ?>
                        </strong>

                        <p>
                            Assets minus liabilities
                        </p>
                    </div>

                </a>

            </div>

        </section>


        <section class="wm-main-grid">

            <article class="wm-panel">

                <div class="wm-panel-heading">

                    <h2>
                        ? Upcoming Bills
                    </h2>

                    <a href="<?= e(
                        url('bills.php')
                    ); ?>">
                        View All
                    </a>

                </div>


                <div class="wm-list">

                    <?php if (
                        $dashboardBills === []
                    ): ?>

                        <div class="wm-empty">
                            No upcoming bills.
                        </div>

                    <?php else: ?>

                        <?php foreach (
                            $dashboardBills as $bill
                        ): ?>

                            <div class="wm-list-row">

                                <div
                                    class="wm-row-icon wm-row-icon-warning"
                                >
                                    ?
                                </div>

                                <div class="wm-row-copy">

                                    <strong>
                                        <?= e(
                                            $bill[
                                                'bill_name'
                                            ]
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


                                <div class="wm-row-end">

                                    <strong>
                                        <?= money(
                                            (float) $bill[
                                                'expected_amount'
                                            ]
                                        ); ?>
                                    </strong>

                                    <span
                                        class="wm-pill <?= (
                                            $bill[
                                                'bill_status'
                                            ] === 'due_soon' ||
                                            $bill[
                                                'bill_status'
                                            ] === 'overdue'
                                        )
                                            ? 'wm-pill-danger'
                                            : ''; ?>"
                                    >
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

                    <?php endif; ?>

                </div>

            </article>


            <article class="wm-panel wm-transactions-panel">

                <div class="wm-panel-heading">

                    <h2>
                        ? Recent Transactions
                    </h2>

                    <a href="<?= e(
                        url('transactions.php')
                    ); ?>">
                        View All
                    </a>

                </div>


                <div class="wm-list">

                    <?php foreach (
                        $recentTransactions as $transaction
                    ): ?>

                        <?php
                        $transactionAmount =
                            (float) (
                                $transaction[
                                    'amount'
                                ]
                                ?? 0
                            );
                        ?>

                        <div class="wm-list-row">

                            <div class="wm-date-column">

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


                            <div
                                class="wm-row-icon <?= (
                                    $transactionAmount >= 0
                                )
                                    ? 'wm-row-icon-success'
                                    : 'wm-row-icon-blue'; ?>"
                            >
                                <?= (
                                    $transactionAmount >= 0
                                )
                                    ? '?'
                                    : '?'; ?>
                            </div>


                            <div class="wm-row-copy">

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
                                    •
                                    <?= e(
                                        $transaction[
                                            'account_name'
                                        ]
                                    ); ?>
                                </small>

                            </div>


                            <strong
                                class="wm-transaction-amount <?= (
                                    $transactionAmount >= 0
                                )
                                    ? 'positive'
                                    : 'negative'; ?>"
                            >
                                <?= (
                                    $transactionAmount >= 0
                                )
                                    ? '+ '
                                    : '- '; ?>

                                <?= money(
                                    abs(
                                        $transactionAmount
                                    )
                                ); ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>


            <article class="wm-panel wm-gains-panel">

                <div class="wm-panel-heading">

                    <h2>
                        ? Gains
                    </h2>

                    <span class="wm-time-pill">
                        Recent
                    </span>

                </div>


                <div class="wm-gains-summary">

                    <small>
                        Positive movements
                    </small>

                    <strong>
                        + <?= money(
                            $positiveTotal
                        ); ?>
                    </strong>

                    <div class="wm-gains-chart">
                        <span></span>
                    </div>

                </div>


                <h3>
                    Top Positive Movements
                </h3>


                <div class="wm-gain-list">

                    <?php if (
                        $positiveTransactions === []
                    ): ?>

                        <div class="wm-empty">
                            No positive movements yet.
                        </div>

                    <?php else: ?>

                        <?php foreach (
                            $positiveTransactions
                            as $transaction
                        ): ?>

                            <div class="wm-gain-row">

                                <div
                                    class="wm-row-icon wm-row-icon-success"
                                >
                                    ?
                                </div>

                                <div class="wm-row-copy">

                                    <strong>
                                        <?= e(
                                            $transaction[
                                                'description'
                                            ]
                                        ); ?>
                                    </strong>

                                    <small>
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
                                        •
                                        <?= e(
                                            $transaction[
                                                'category'
                                            ]
                                        ); ?>
                                    </small>

                                </div>

                                <strong class="positive">
                                    + <?= money(
                                        (float) $transaction[
                                            'amount'
                                        ]
                                    ); ?>
                                </strong>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </article>

        </section>


        <section class="wm-bottom-grid">

            <article class="wm-crypto-snapshot">

                <div class="wm-crypto-head">

                    <h2>
                        ? Crypto Snapshot
                    </h2>

                    <a href="<?= e(
                        url('crypto.php')
                    ); ?>">
                        View All
                    </a>

                </div>


                <div class="wm-crypto-body">

                    <div>

                        <small>
                            Total Crypto Value
                        </small>

                        <strong>
                            <?= money(
                                (float) $summary[
                                    'crypto'
                                ]
                            ); ?>
                        </strong>

                    </div>

                    <div class="wm-crypto-line">
                        <span></span>
                    </div>

                </div>

            </article>


            <article class="wm-status-banner">

                <div class="wm-status-icon">
                    ??
                </div>

                <div class="wm-status-copy">

                    <strong>
                        Your money dashboard is live.
                    </strong>

                    <span>
                        Accounts, bills, transactions and crypto
                        are feeding the same picture.
                    </span>

                </div>


                <a
                    href="<?= e(
                        url('reports.php')
                    ); ?>"
                    class="wm-status-button"
                >
                    ? View Full Report
                </a>

            </article>

        </section>

    </main>

</div>


<script>
(function () {

    const button =
        document.getElementById(
            'dashboard-plaid-sync'
        );

    if (!button) {
        return;
    }

    button.addEventListener(
        'click',
        async function () {

            button.disabled = true;
            button.textContent =
                '? Syncing...';

            try {

                const response =
                    await fetch(
                        '<?= e(
                            url(
                                'plaid-sync.php'
                            )
                        ); ?>',
                        {
                            credentials:
                                'same-origin',
                            cache:
                                'no-store'
                        }
                    );

                const text =
                    await response.text();

                if (
                    !response.ok ||
                    text
                        .toLowerCase()
                        .includes('error')
                ) {
                    throw new Error(
                        text ||
                        'Sync failed.'
                    );
                }

                button.textContent =
                    '? Synced';

                setTimeout(
                    function () {
                        window
                            .location
                            .reload();
                    },
                    500
                );

            } catch (error) {

                button.disabled =
                    false;

                button.textContent =
                    '? Sync Now';

                alert(
                    'Sync failed: '
                    + error.message
                );
            }
        }
    );

})();
</script>


