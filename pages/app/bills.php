<div class="app-layout">

    <main class="app-main">

        <header class="bills-header">

            <div>

                <p class="app-greeting">
                    Recurring obligations
                </p>

                <h1>
                    Bills
                </h1>

                <p class="bills-subtitle">
                    See what's due, what's paid,
                    and what's coming next.
                </p>

            </div>

            <div class="bills-actions">

                <button
                    class="secondary-button"
                    type="button"
                >
                    Find Recurring
                </button>

                <button
                    class="primary-button"
                    type="button"
                >
                    + Add Bill
                </button>

            </div>

        </header>


        <section class="bill-summary-grid">

            <article>

                <span>
                    Upcoming
                </span>

                <strong>
                    <?= money($totalUpcoming); ?>
                </strong>

                <small>
                    Remaining tracked bills
                </small>

            </article>


            <article>

                <span>
                    Paid
                </span>

                <strong class="positive">
                    <?= money($totalPaid); ?>
                </strong>

                <small>
                    Paid this cycle
                </small>

            </article>


            <article>

                <span>
                    Due Soon
                </span>

                <strong>
                    <?= $dueSoonCount; ?>
                </strong>

                <small>
                    Needs attention soon
                </small>

            </article>


            <article>

                <span>
                    Bills Tracked
                </span>

                <strong>
                    <?= count($bills); ?>
                </strong>

                <small>
                    Recurring obligations
                </small>

            </article>

        </section>


        <section class="bills-layout">

            <div class="bills-card">

                <div class="bills-card-heading">

                    <div>

                        <small>
                            Schedule
                        </small>

                        <h2>
                            Upcoming Bills
                        </h2>

                    </div>

                    <div class="bill-filters">

                        <button
                            class="active"
                            type="button"
                        >
                            All
                        </button>

                        <button type="button">
                            Upcoming
                        </button>

                        <button type="button">
                            Paid
                        </button>

                        <button type="button">
                            Overdue
                        </button>

                    </div>

                </div>


                <div class="bills-table">

                    <div class="bills-table-head">

                        <span>Bill</span>
                        <span>Due</span>
                        <span>Frequency</span>
                        <span>Account</span>
                        <span>Autopay</span>
                        <span>Amount</span>
                        <span>Status</span>

                    </div>


                    <?php foreach ($bills as $bill): ?>

                        <article class="bills-table-row">

                            <div class="bill-name-cell">

                                <span class="bill-icon">

                                    <?= e(
                                        strtoupper(
                                            substr(
                                                $bill['name'],
                                                0,
                                                1
                                            )
                                        )
                                    ); ?>

                                </span>

                                <div>

                                    <strong>
                                        <?= e($bill['name']); ?>
                                    </strong>

                                    <small>
                                        <?= e($bill['category']); ?>
                                    </small>

                                </div>

                            </div>


                            <div>
                                <?= e($bill['due']); ?>
                            </div>


                            <div>
                                <?= e($bill['frequency']); ?>
                            </div>


                            <div>
                                <?= e($bill['account']); ?>
                            </div>


                            <div>

                                <span
                                    class="autopay-badge <?= $bill['autopay']
                                        ? 'on'
                                        : 'off'; ?>"
                                >
                                    <?= $bill['autopay']
                                        ? 'On'
                                        : 'Off'; ?>
                                </span>

                            </div>


                            <div class="bill-amount-cell">

                                <strong>
                                    <?= money(
                                        (float) $bill['amount']
                                    ); ?>
                                </strong>

                            </div>


                            <div>

                                <span
                                    class="bill-status-badge <?= strtolower(
                                        str_replace(
                                            ' ',
                                            '-',
                                            $bill['status']
                                        )
                                    ); ?>"
                                >
                                    <?= e($bill['status']); ?>
                                </span>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </div>


            <aside class="bill-side-card">

                <small>
                    Bill health
                </small>

                <h2>
                    Monthly Overview
                </h2>

                <div class="bill-health-list">

                    <div>

                        <span>
                            Total scheduled
                        </span>

                        <strong>
                            <?= money(
                                $totalUpcoming + $totalPaid
                            ); ?>
                        </strong>

                    </div>

                    <div>

                        <span>
                            Already paid
                        </span>

                        <strong class="positive">
                            <?= money($totalPaid); ?>
                        </strong>

                    </div>

                    <div>

                        <span>
                            Still upcoming
                        </span>

                        <strong>
                            <?= money($totalUpcoming); ?>
                        </strong>

                    </div>

                </div>


                <div class="bill-note">

                    <strong>
                        Variable bills
                    </strong>

                    <p>
                        Utility, phone, and other changing
                        bills can be updated when new
                        transactions arrive.
                    </p>

                </div>

            </aside>

        </section>

    </main>

</div>
