<div class="app-layout">

    <main class="app-main">

        <header class="accounts-header">

            <div>

                <p class="app-greeting">
                    Your financial accounts
                </p>

                <h1>
                    Accounts
                </h1>

                <p class="accounts-subtitle">
                    Every place your money lives,
                    moves through, or is tracked.
                </p>

            </div>

            <div class="accounts-actions">

                <button
                    class="secondary-button"
                    type="button"
                >
                    Add Manual Account
                </button>

                <button
                    class="primary-button"
                    type="button"
                >
                    + Link Account
                </button>

            </div>

        </header>


        <section class="account-summary-grid">

            <article>

                <span>
                    Total Cash
                </span>

                <strong>
                    <?= money($cashTotal); ?>
                </strong>

                <small>
                    Across tracked cash accounts
                </small>

            </article>


            <article>

                <span>
                    Accounts
                </span>

                <strong>
                    <?= count($accounts); ?>
                </strong>

                <small>
                    Linked and manual sources
                </small>

            </article>


            <article>

                <span>
                    Available
                </span>

                <strong>
                    <?= money($cashTotal); ?>
                </strong>

                <small>
                    Currently available funds
                </small>

            </article>

        </section>


        <section class="accounts-card">

            <div class="accounts-card-header">

                <div>

                    <small>
                        Money sources
                    </small>

                    <h2>
                        Your Accounts
                    </h2>

                </div>


                <div class="account-filters">

                    <button
                        class="active"
                        type="button"
                    >
                        All
                    </button>

                    <button type="button">
                        Cash
                    </button>

                    <button type="button">
                        Credit
                    </button>

                    <button type="button">
                        Investments
                    </button>

                </div>

            </div>


            <div class="accounts-table">

                <div class="accounts-table-head">

                    <span>
                        Account
                    </span>

                    <span>
                        Source
                    </span>

                    <span>
                        Type
                    </span>

                    <span>
                        Available
                    </span>

                    <span>
                        Balance
                    </span>

                </div>


                <?php foreach ($accounts as $account): ?>

                    <article class="accounts-table-row">

                        <div class="account-name-cell">

                            <span class="account-logo">
                                <?= e($account['icon']); ?>
                            </span>

                            <div>

                                <strong>
                                    <?= e($account['name']); ?>
                                </strong>

                                <small>
                                    <?= e($account['institution']); ?>

                                    <?php if ($account['last4'] !== ''): ?>
                                        ****<?= e($account['last4']); ?>
                                    <?php endif; ?>
                                </small>

                            </div>

                        </div>


                        <div>

                            <span class="source-badge">
                                <?= e($account['source']); ?>
                            </span>

                        </div>


                        <div class="account-type-cell">

                            <strong>
                                <?= e($account['type']); ?>
                            </strong>

                        </div>


                        <div class="account-money-cell">

                            <?= money(
                                (float) $account['available']
                            ); ?>

                        </div>


                        <div class="account-money-cell">

                            <strong>
                                <?= money(
                                    (float) $account['balance']
                                ); ?>
                            </strong>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>

    </main>

</div>
