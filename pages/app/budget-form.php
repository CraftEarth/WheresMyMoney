<div class="app-layout">

    <main class="app-main">

        <header class="budgets-header">

            <div>

                <p class="app-greeting">
                    <?= $budgetId > 0
                        ? 'Update spending limit'
                        : 'Create spending limit'; ?>
                </p>

                <h1>
                    <?= $budgetId > 0
                        ? 'Edit Budget'
                        : 'Add Budget'; ?>
                </h1>

            </div>

        </header>


        <section class="account-form-card">

            <?php if ($errorMessage !== null): ?>

                <div class="settings-message error-message">
                    <?= e($errorMessage); ?>
                </div>

            <?php endif; ?>


            <form
                method="post"
                class="account-form"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()); ?>"
                >


                <div class="account-form-grid">

                    <label>

                        <span>
                            Category
                        </span>

                        <input
                            type="text"
                            name="category"
                            value="<?= e(
                                $budget['category']
                                ?? ''
                            ); ?>"
                            placeholder="Other"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Monthly Limit
                        </span>

                        <input
                            type="number"
                            name="monthly_limit"
                            step="0.01"
                            min="0.01"
                            value="<?= e(
                                isset(
                                    $budget['monthly_limit']
                                )
                                    ? (string) $budget[
                                        'monthly_limit'
                                    ]
                                    : ''
                            ); ?>"
                            placeholder="100.00"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Month
                        </span>

                        <input
                            type="month"
                            name="budget_month"
                            value="<?= e(
                                isset(
                                    $budget['budget_month']
                                )
                                    ? date(
                                        'Y-m',
                                        strtotime(
                                            $budget[
                                                'budget_month'
                                            ]
                                        )
                                    )
                                    : date('Y-m')
                            ); ?>"
                            required
                        >

                    </label>

                </div>


                <div class="account-form-actions">

                    <a
                        class="secondary-button"
                        href="<?= e(
                            url('budgets.php')
                        ); ?>"
                    >
                        Cancel
                    </a>

                    <button
                        class="primary-button"
                        type="submit"
                    >
                        <?= $budgetId > 0
                            ? 'Save Changes'
                            : 'Add Budget'; ?>
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>
