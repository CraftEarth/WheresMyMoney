<div class="app-layout">

    <main class="app-main">

        <header class="budgets-header">

            <div>

                <p class="app-greeting">
                    Spending plan
                </p>

                <h1>
                    Budgets
                </h1>

                <p class="budgets-subtitle">
                    Your real transaction spending
                    compared against the limits you set.
                </p>

            </div>

            <div class="budgets-actions">

                <a
                    class="primary-button"
                    href="<?= e(
                        url('budget-form.php')
                    ); ?>"
                >
                    + Add Budget
                </a>

            </div>

        </header>


        <section class="budget-summary-grid">

            <article>

                <span>
                    Monthly Budget
                </span>

                <strong>
                    <?= money($totalBudget); ?>
                </strong>

                <small>
                    Total planned spending
                </small>

            </article>


            <article>

                <span>
                    Spent
                </span>

                <strong class="negative">
                    <?= money($totalSpent); ?>
                </strong>

                <small>
                    Real posted expenses
                </small>

            </article>


            <article>

                <span>
                    Remaining
                </span>

                <strong class="<?= $totalRemaining >= 0
                    ? 'positive'
                    : 'negative'; ?>"
                >
                    <?= $totalRemaining >= 0
                        ? money($totalRemaining)
                        : '-' . money(
                            abs($totalRemaining)
                        ); ?>
                </strong>

                <small>
                    Budget still available
                </small>

            </article>


            <article>

                <span>
                    Categories
                </span>

                <strong>
                    <?= count($budgets); ?>
                </strong>

                <small>
                    Active budgets this month
                </small>

            </article>

        </section>


        <section class="budgets-card">

            <div class="budgets-card-heading">

                <div>

                    <small>
                        This month
                    </small>

                    <h2>
                        Category Budgets
                    </h2>

                </div>

                <div class="budget-month">
                    <?= e(
                        date('F Y')
                    ); ?>
                </div>

            </div>


            <?php if ($budgets === []): ?>

                <div class="accounts-empty">

                    <strong>
                        No budgets yet.
                    </strong>

                    <p>
                        Add a category budget and its
                        real transaction spending will
                        appear automatically.
                    </p>

                    <a
                        class="primary-button"
                        href="<?= e(
                            url('budget-form.php')
                        ); ?>"
                    >
                        Add Budget
                    </a>

                </div>

            <?php else: ?>

                <div class="budget-list">

                    <?php foreach ($budgets as $budget): ?>

                        <article class="budget-row">

                            <div class="budget-row-top">

                                <div>

                                    <strong>
                                        <?= e(
                                            $budget['category']
                                        ); ?>
                                    </strong>

                                    <small>
                                        <?= money(
                                            (float) $budget['spent']
                                        ); ?>
                                        spent of
                                        <?= money(
                                            (float) $budget[
                                                'monthly_limit'
                                            ]
                                        ); ?>
                                    </small>

                                </div>


                                <div class="budget-remaining">

                                    <span>
                                        Remaining
                                    </span>

                                    <strong class="<?= (
                                        (float) $budget[
                                            'remaining'
                                        ] >= 0
                                    )
                                        ? 'positive'
                                        : 'negative'; ?>"
                                    >

                                        <?php if (
                                            (float) $budget[
                                                'remaining'
                                            ] >= 0
                                        ): ?>

                                            <?= money(
                                                (float) $budget[
                                                    'remaining'
                                                ]
                                            ); ?>

                                        <?php else: ?>

                                            -<?= money(
                                                abs(
                                                    (float) $budget[
                                                        'remaining'
                                                    ]
                                                )
                                            ); ?>

                                        <?php endif; ?>

                                    </strong>

                                </div>

                            </div>


                            <div class="budget-progress">

                                <span
                                    class="<?= (
                                        (float) $budget[
                                            'percent'
                                        ] >= 90
                                    )
                                        ? 'near-limit'
                                        : ''; ?>"
                                    style="width: <?= min(
                                        100,
                                        (int) round(
                                            $budget[
                                                'percent'
                                            ]
                                        )
                                    ); ?>%;"
                                ></span>

                            </div>


                            <div class="budget-row-bottom">

                                <small>
                                    <?= number_format(
                                        (float) $budget[
                                            'percent'
                                        ],
                                        1
                                    ); ?>% used
                                </small>


                                <div class="budget-actions">

                                    <a
                                        href="<?= e(
                                            url(
                                                'budget-form.php?id='
                                                . $budget['id']
                                            )
                                        ); ?>"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        method="post"
                                        action="<?= e(
                                            url(
                                                'budget-archive.php'
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
                                            name="budget_id"
                                            value="<?= (int) $budget[
                                                'id'
                                            ]; ?>"
                                        >

                                        <button type="submit">
                                            Archive
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>
