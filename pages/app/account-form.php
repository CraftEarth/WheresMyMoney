<div class="app-layout">

    <main class="app-main">

        <header class="accounts-header">

            <div>

                <p class="app-greeting">
                    <?= $accountId > 0
                        ? 'Update account'
                        : 'Add money source'; ?>
                </p>

                <h1>
                    <?= $accountId > 0
                        ? 'Edit Account'
                        : 'Add Account'; ?>
                </h1>

                <p class="accounts-subtitle">
                    Manual accounts can later be replaced
                    or merged with linked accounts.
                </p>

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
                            Account Name
                        </span>

                        <input
                            type="text"
                            name="account_name"
                            value="<?= e(
                                $account['account_name']
                                ?? ''
                            ); ?>"
                            placeholder="Chime Checking"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Institution
                        </span>

                        <input
                            type="text"
                            name="institution_name"
                            value="<?= e(
                                $account['institution_name']
                                ?? ''
                            ); ?>"
                            placeholder="Chime"
                        >

                    </label>


                    <label>

                        <span>
                            Account Type
                        </span>

                        <select
                            name="account_type"
                            required
                        >

                            <?php
                            $types = [
                                'checking' => 'Checking',
                                'savings' => 'Savings',
                                'cash' => 'Cash',
                                'wallet' => 'Digital Wallet',
                                'credit' => 'Credit Card',
                                'loan' => 'Loan',
                                'investment' => 'Investment',
                                'retirement' => 'Retirement',
                            ];
                            ?>


                            <?php foreach (
                                $types as $value => $label
                            ): ?>

                                <option
                                    value="<?= e($value); ?>"
                                    <?= (
                                        $account['account_type']
                                        ?? 'checking'
                                    ) === $value
                                        ? 'selected'
                                        : ''; ?>
                                >
                                    <?= e($label); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </label>


                    <label>

                        <span>
                            Last 4 Digits
                        </span>

                        <input
                            type="text"
                            name="last4"
                            maxlength="4"
                            value="<?= e(
                                $account['last4']
                                ?? ''
                            ); ?>"
                            placeholder="1234"
                        >

                    </label>


                    <label>

                        <span>
                            Current Balance
                        </span>

                        <input
                            type="number"
                            name="balance_current"
                            step="0.01"
                            value="<?= e(
                                isset($account['balance_current'])
                                    ? (string) $account['balance_current']
                                    : '0.00'
                            ); ?>"
                        >

                    </label>


                    <label>

                        <span>
                            Available Balance
                        </span>

                        <input
                            type="number"
                            name="balance_available"
                            step="0.01"
                            value="<?= e(
                                isset($account['balance_available'])
                                    ? (string) $account['balance_available']
                                    : '0.00'
                            ); ?>"
                        >

                    </label>

                </div>


                <div class="account-toggle-list">

                    <label class="settings-toggle-row">

                        <div>

                            <strong>
                                Include in cash total
                            </strong>

                            <small>
                                Checking, savings, wallets,
                                and cash usually belong here.
                            </small>

                        </div>

                        <input
                            type="checkbox"
                            name="include_in_cash"
                            value="1"
                            <?= !isset($account)
                                || !empty(
                                    $account['include_in_cash']
                                )
                                    ? 'checked'
                                    : ''; ?>
                        >

                    </label>


                    <label class="settings-toggle-row">

                        <div>

                            <strong>
                                Include in net worth
                            </strong>

                            <small>
                                Count this account in your
                                financial position.
                            </small>

                        </div>

                        <input
                            type="checkbox"
                            name="include_in_net_worth"
                            value="1"
                            <?= !isset($account)
                                || !empty(
                                    $account['include_in_net_worth']
                                )
                                    ? 'checked'
                                    : ''; ?>
                        >

                    </label>


                    <label class="settings-toggle-row">

                        <div>

                            <strong>
                                Include in safe to spend
                            </strong>

                            <small>
                                Usually enabled only for
                                spendable cash accounts.
                            </small>

                        </div>

                        <input
                            type="checkbox"
                            name="include_in_safe_to_spend"
                            value="1"
                            <?= !isset($account)
                                || !empty(
                                    $account[
                                        'include_in_safe_to_spend'
                                    ]
                                )
                                    ? 'checked'
                                    : ''; ?>
                        >

                    </label>

                </div>


                <div class="account-form-actions">

                    <a
                        class="secondary-button"
                        href="<?= e(url('accounts.php')); ?>"
                    >
                        Cancel
                    </a>

                    <button
                        class="primary-button"
                        type="submit"
                    >
                        <?= $accountId > 0
                            ? 'Save Changes'
                            : 'Add Account'; ?>
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>
