<div class="app-layout">

    <main class="app-main">

        <header class="reports-header">

            <div>

                <p class="app-greeting">
                    Financial analysis
                </p>

                <h1>
                    Reports
                </h1>

                <p class="reports-subtitle">
                    Real cash flow, spending,
                    bills, and budget performance.
                </p>

            </div>

            <div class="reports-actions">

                <button
                    class="secondary-button"
                    type="button"
                    onclick="window.print();"
                >
                    Print
                </button>

            </div>

        </header>


        <section class="report-summary-grid">

            <article>

                <span>
                    Income
                </span>

                <strong class="positive">
                    +<?= money($currentIncome); ?>
                </strong>

                <small>
                    <?= e(date('F Y')); ?>
                </small>

            </article>


            <article>

                <span>
                    Spending
                </span>

                <strong class="negative">
                    -<?= money($currentSpending); ?>
                </strong>

                <small>
                    Transfers excluded
                </small>

            </article>


            <article>

                <span>
                    Net Cash Flow
                </span>

                <strong class="<?= $netCashFlow >= 0
                    ? 'positive'
                    : 'negative'; ?>"
                >
                    <?= $netCashFlow >= 0
                        ? '+'
                        : '-'; ?>

                    <?= money(
                        abs($netCashFlow)
                    ); ?>
                </strong>

                <small>
                    Income minus spending
                </small>

            </article>


            <article>

                <span>
                    Savings Rate
                </span>

                <strong>
                    <?= number_format(
                        $savingsRate,
                        1
                    ); ?>%
                </strong>

                <small>
                    Based on tracked income
                </small>

            </article>

        </section>


        <section class="reports-grid">

            <article class="reports-card">

                <div class="reports-card-heading">

                    <div>

                        <small>
                            Month to month
                        </small>

                        <h2>
                            Cash Flow History
                        </h2>

                    </div>

                </div>


                <div class="report-history">

                    <?php foreach (
                        $monthlyHistory as $month
                    ): ?>

                        <div class="report-history-row">

                            <div>

                                <strong>
                                    <?= e($month['month']); ?>
                                </strong>

                                <small>
                                    Posted transactions
                                </small>

                            </div>


                            <div>

                                <span>
                                    Income
                                </span>

                                <strong class="positive">
                                    +<?= money(
                                        $month['income']
                                    ); ?>
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Spending
                                </span>

                                <strong class="negative">
                                    -<?= money(
                                        $month['spending']
                                    ); ?>
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Net
                                </span>

                                <strong class="<?= (
                                    $month['net'] >= 0
                                )
                                    ? 'positive'
                                    : 'negative'; ?>"
                                >
                                    <?= $month['net'] >= 0
                                        ? '+'
                                        : '-'; ?>

                                    <?= money(
                                        abs(
                                            $month['net']
                                        )
                                    ); ?>
                                </strong>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>


            <article class="reports-card">

                <div class="reports-card-heading">

                    <div>

                        <small>
                            This month
                        </small>

                        <h2>
                            Spending Breakdown
                        </h2>

                    </div>

                </div>


                <?php if ($categories === []): ?>

                    <div class="dashboard-empty">
                        No expense data this month.
                    </div>

                <?php else: ?>

                    <div class="report-category-list">

                        <?php foreach (
                            $categories as $category
                        ): ?>

                            <div class="report-category-row">

                                <div class="report-category-top">

                                    <span>
                                        <?= e(
                                            $category['category']
                                        ); ?>
                                    </span>

                                    <strong>
                                        <?= money(
                                            (float) $category[
                                                'amount'
                                            ]
                                        ); ?>
                                    </strong>

                                </div>


                                <div class="report-category-track">

                                    <span
                                        style="width: <?= min(
                                            100,
                                            (int) round(
                                                $category[
                                                    'percent'
                                                ]
                                            )
                                        ); ?>%;"
                                    ></span>

                                </div>


                                <small>
                                    <?= number_format(
                                        (float) $category[
                                            'percent'
                                        ],
                                        1
                                    ); ?>%
                                    of spending
                                </small>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </article>


            <article class="reports-card report-wide">

                <div class="reports-card-heading">

                    <div>

                        <small>
                            Current position
                        </small>

                        <h2>
                            Where Did My Money Go?
                        </h2>

                    </div>

                </div>


                <div class="report-insight-grid">

                    <div>

                        <span>
                            Largest Category
                        </span>

                        <strong>
                            <?= e(
                                $largestCategory[
                                    'category'
                                ]
                                ?? 'None'
                            ); ?>
                        </strong>

                        <small>
                            <?= $largestCategory
                                ? money(
                                    (float) $largestCategory[
                                        'amount'
                                    ]
                                )
                                : '$0.00'; ?>
                        </small>

                    </div>


                    <div>

                        <span>
                            Upcoming Bills
                        </span>

                        <strong>
                            <?= money($upcomingBills); ?>
                        </strong>

                        <small>
                            Unpaid obligations
                        </small>

                    </div>


                    <div>

                        <span>
                            Budgeted
                        </span>

                        <strong>
                            <?= money($totalBudget); ?>
                        </strong>

                        <small>
                            Current month limits
                        </small>

                    </div>


                    <div>

                        <span>
                            Habit Spending
                        </span>

                        <strong>
                            <?= money(
                                $totalHabitSpending
                            ); ?>
                        </strong>

                        <small>
                            Cannabis + Alcohol
                        </small>

                    </div>

                </div>

            </article>

        </section>

    </main>

</div>
