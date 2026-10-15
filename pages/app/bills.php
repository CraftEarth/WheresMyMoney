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
                    Track what is due, paid,
                    overdue, and coming next.
                </p>

            </div>

            <div class="bills-actions">

                <a
                    class="primary-button"
                    href="<?= e(
                        url('bill-form.php')
                    ); ?>"
                >
                    + Add Bill
                </a>

            </div>

        </header>


        <section class="bill-summary-grid">

            <article>
                <span>Upcoming</span>

                <strong>
                    <?= money($totalUpcoming); ?>
                </strong>

                <small>
                    Unpaid tracked bills
                </small>
            </article>


            <article>
                <span>Paid</span>

                <strong class="positive">
                    <?= money($totalPaid); ?>
                </strong>

                <small>
                    Marked paid
                </small>
            </article>


            <article>
                <span>Due Soon</span>

                <strong>
                    <?= $dueSoonCount; ?>
                </strong>

                <small>
                    Needs attention
                </small>
            </article>


            <article>
                <span>Bills Tracked</span>

                <strong>
                    <?= count($bills); ?>
                </strong>

                <small>
                    Active bill records
                </small>
            </article>

        </section>


        <section class="bills-card">

            <?php if ($bills === []): ?>

                <div class="accounts-empty">

                    <strong>
                        No bills yet.
                    </strong>

                    <p>
                        Add your first bill to begin
                        calculating safe-to-spend.
                    </p>

                    <a
                        class="primary-button"
                        href="<?= e(
                            url('bill-form.php')
                        ); ?>"
                    >
                        Add Bill
                    </a>

                </div>

            <?php else: ?>

                <div class="bills-table">

                    <div class="bills-table-head">

                        <span>Bill</span>
                        <span>Due</span>
                        <span>Frequency</span>
                        <span>Account</span>
                        <span>Autopay</span>
                        <span>Amount</span>
                        <span>Status</span>
                        <span>Actions</span>

                    </div>


                    <?php foreach ($bills as $bill): ?>

                        <article class="bills-table-row">

                            <div class="bill-name-cell">

                                <span class="bill-icon">
                                    <?= e(
                                        strtoupper(
                                            substr(
                                                $bill['bill_name'],
                                                0,
                                                1
                                            )
                                        )
                                    ); ?>
                                </span>

                                <div>

                                    <strong>
                                        <?= e($bill['bill_name']); ?>
                                    </strong>

                                    <small>
                                        <?= e($bill['category']); ?>
                                    </small>

                                </div>

                            </div>


                            <div>
                                <?= e(
                                    date(
                                        'M j',
                                        strtotime(
                                            $bill['next_due_date']
                                        )
                                    )
                                ); ?>
                            </div>


                            <div>
                                <?= e(
                                    ucfirst(
                                        $bill['frequency']
                                    )
                                ); ?>
                            </div>


                            <div>
                                <?= e(
                                    $bill['account_name']
                                    ?? 'Not assigned'
                                ); ?>
                            </div>


                            <div>

                                <span
                                    class="autopay-badge <?= (
                                        (int) $bill['autopay'] === 1
                                    )
                                        ? 'on'
                                        : 'off'; ?>"
                                >
                                    <?= (
                                        (int) $bill['autopay'] === 1
                                    )
                                        ? 'On'
                                        : 'Off'; ?>
                                </span>

                            </div>


                            <div class="bill-amount-cell">

                                <strong>
                                    <?= money(
                                        (float) $bill[
                                            'expected_amount'
                                        ]
                                    ); ?>
                                </strong>

                            </div>


                            <div>

                                <span
                                    class="bill-status-badge <?= e(
                                        str_replace(
                                            '_',
                                            '-',
                                            $bill['bill_status']
                                        )
                                    ); ?>"
                                >
                                    <?= e(
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $bill['bill_status']
                                            )
                                        )
                                    ); ?>
                                </span>

                            </div>


                            <div class="bill-actions-cell">

                                <a
                                    href="<?= e(
                                        url(
                                            'bill-form.php?id='
                                            . $bill['id']
                                        )
                                    ); ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    method="post"
                                    action="<?= e(
                                        url('bill-archive.php')
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
                                        name="bill_id"
                                        value="<?= (int) $bill['id']; ?>"
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
