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
                    Review cash flow, spending patterns,
                    categories, and month-to-month changes.
                </p>

            </div>

            <div class="reports-actions">

                <button
                    class="secondary-button"
                    type="button"
                >
                    Export
                </button>

                <button
                    class="primary-button"
                    type="button"
                >
                    Print Report
                </button>

            </div>

        </header>


        <section class="report-summary-grid">

            <article>

                <span>
                    Income
                </span>

                <strong class="positive">
                    +<?= money((float) $current['income']); ?>
                </strong>

                <small>
                    October 2026
                </small>

            </article>


            <article>

                <span>
                    Spending
                </span>

                <strong class="negative">
                    -<?= money((float) $current['spending']); ?>
                </strong>

                <small>
                    Excludes transfers
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
                    <?= $netCashFlow >= 0 ? '+' : '-'; ?>
                    <?= money(abs($netCashFlow)); ?>
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
                    <?= number_format($savingsRate, 1); ?>%
                </strong>

                <small>
                    Based on tracked cash flow
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

                    <?php foreach ($monthly as $month): ?>

                        <?php
                        $monthNet =
                            (float) $month['income']
                            - (float) $month['spending'];
                        ?>

                        <div class="report-history-row">

                            <div>

                                <strong>
                                    <?= e($month['month']); ?>
                                </strong>

                                <small>
                                    Tracked activity
                                </small>

                            </div>


                            <div>

                                <span>
                                    Income
                                </span>

                                <strong class="positive">
                                    +<?= money(
                                        (float) $month['income']
                                    ); ?>
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Spending
                                </span>

                                <strong class="negative">
                                    -<?= money(
                                        (float) $month['spending']
                                    ); ?>
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Net
                                </span>

                                <strong class="<?= $monthNet >= 0
                                    ? 'positive'
                                    : 'negative'; ?>"
                                >
                                    <?= $monthNet >= 0 ? '+' : '-'; ?>
                                    <?= money(abs($monthNet)); ?>
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
                            Categories
                        </small>

                        <h2>
                            Spending Breakdown
                        </h2>

                    </div>

                </div>


                <div class="report-category-list">

                    <?php foreach ($categories as $category): ?>

                        <div class="report-category-row">

                            <div class="report-category-top">

                                <span>
                                    <?= e($category['name']); ?>
                                </span>

                                <strong>
                                    <?= money(
                                        (float) $category['amount']
                                    ); ?>
                                </strong>

                            </div>

                            <div class="report-category-track">

                                <span
                                    style="width: <?= (int) $category['percent']; ?>%;"
                                ></span>

                            </div>

                            <small>
                                <?= (int) $category['percent']; ?>%
                                of tracked spending
                            </small>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>


            <article class="reports-card report-wide">

                <div class="reports-card-heading">

                    <div>

                        <small>
                            Summary
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
                            Groceries
                        </strong>

                        <small>
                            $349.21 tracked
                        </small>

                    </div>


                    <div>

                        <span>
                            Bills This Month
                        </span>

                        <strong>
                            <?= money((float) $current['bills']); ?>
                        </strong>

                        <small>
                            Scheduled obligations
                        </small>

                    </div>


                    <div>

                        <span>
                            Habit Spending
                        </span>

                        <strong>
                            $174.22
                        </strong>

                        <small>
                            Cannabis + Alcohol
                        </small>

                    </div>


                    <div>

                        <span>
                            Cash Flow
                        </span>

                        <strong class="<?= $netCashFlow >= 0
                            ? 'positive'
                            : 'negative'; ?>"
                        >
                            <?= $netCashFlow >= 0 ? '+' : '-'; ?>
                            <?= money(abs($netCashFlow)); ?>
                        </strong>

                        <small>
                            Income minus spending
                        </small>

                    </div>

                </div>

            </article>

        </section>

    </main>

</div>
