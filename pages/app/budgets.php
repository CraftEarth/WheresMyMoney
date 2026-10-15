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
                    Set limits, compare actual spending,
                    and see what remains.
                </p>

            </div>

            <div class="budgets-actions">

                <button
                    class="secondary-button"
                    type="button"
                >
                    Copy Last Month
                </button>

                <button
                    class="primary-button"
                    type="button"
                >
                    + Add Budget
                </button>

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
                    Across tracked categories
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
                    This month
                </small>

            </article>


            <article>

                <span>
                    Remaining
                </span>

                <strong class="positive">
                    <?= money($totalRemaining); ?>
                </strong>

                <small>
                    Still available
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
                    With active budgets
                </small>

            </article>

        </section>


        <section class="budgets-layout">

            <div class="budgets-card">

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
                        October 2026
                    </div>

                </div>


                <div class="budget-list">

                    <?php foreach ($budgets as $budget): ?>

                        <article class="budget-row">

                            <div class="budget-row-top">

                                <div>

                                    <strong>
                                        <?= e($budget['category']); ?>
                                    </strong>

                                    <small>
                                        <?= money(
                                            (float) $budget['spent']
                                        ); ?>
                                        of
                                        <?= money(
                                            (float) $budget['budget']
                                        ); ?>
                                    </small>

                                </div>

                                <div class="budget-remaining">

                                    <span>
                                        Remaining
                                    </span>

                                    <strong class="<?= $budget['remaining'] >= 0
                                        ? 'positive'
                                        : 'negative'; ?>"
                                    >
                                        <?= money(
                                            abs(
                                                (float) $budget['remaining']
                                            )
                                        ); ?>
                                    </strong>

                                </div>

                            </div>


                            <div class="budget-progress">

                                <span
                                    style="width: <?= (int) $budget['percent']; ?>%;"
                                    class="<?= $budget['percent'] >= 90
                                        ? 'near-limit'
                                        : ''; ?>"
                                ></span>

                            </div>


                            <div class="budget-row-bottom">

                                <small>
                                    <?= (int) $budget['percent']; ?>% used
                                </small>

                                <button type="button">
                                    Edit
                                </button>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </div>


            <aside class="budget-side-card">

                <small>
                    Budget overview
                </small>

                <h2>
                    Monthly Position
                </h2>


                <div class="budget-side-stat">

                    <span>
                        Planned
                    </span>

                    <strong>
                        <?= money($totalBudget); ?>
                    </strong>

                </div>


                <div class="budget-side-stat">

                    <span>
                        Used
                    </span>

                    <strong>
                        <?= money($totalSpent); ?>
                    </strong>

                </div>


                <div class="budget-side-stat budget-side-highlight">

                    <span>
                        Remaining
                    </span>

                    <strong>
                        <?= money($totalRemaining); ?>
                    </strong>

                </div>


                <div class="budget-note">

                    <strong>
                        Neutral tracking
                    </strong>

                    <p>
                        Budgets show spending against the limits
                        you choose. Categories remain user-defined
                        and judgment-free.
                    </p>

                </div>

            </aside>

        </section>

    </main>

</div>
