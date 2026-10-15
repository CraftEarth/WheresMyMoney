<div class="app-layout">

    <main class="app-main">

        <header class="transactions-header">

            <div>

                <p class="app-greeting">
                    <?= $transactionId > 0
                        ? 'Update transaction'
                        : 'Record money activity'; ?>
                </p>

                <h1>
                    <?= $transactionId > 0
                        ? 'Edit Transaction'
                        : 'Add Transaction'; ?>
                </h1>

                <p class="transactions-subtitle">
                    Transfers are tracked separately
                    so they do not inflate spending.
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
                            Account
                        </span>

                        <select
                            name="account_id"
                            required
                        >

                            <?php foreach (
                                $accounts as $accountOption
                            ): ?>

                                <option
                                    value="<?= (int) $accountOption['id']; ?>"
                                    <?= (
                                        (int) (
                                            $transaction['account_id']
                                            ?? 0
                                        )
                                        ===
                                        (int) $accountOption['id']
                                    )
                                        ? 'selected'
                                        : ''; ?>
                                >
                                    <?= e(
                                        $accountOption[
                                            'account_name'
                                        ]
                                    ); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </label>


                    <label>

                        <span>
                            Date
                        </span>

                        <input
                            type="date"
                            name="transaction_date"
                            value="<?= e(
                                $transaction[
                                    'transaction_date'
                                ]
                                ?? date('Y-m-d')
                            ); ?>"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Description
                        </span>

                        <input
                            type="text"
                            name="description"
                            value="<?= e(
                                $transaction['description']
                                ?? ''
                            ); ?>"
                            placeholder="Marathon"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Category
                        </span>

                        <input
                            type="text"
                            name="category"
                            value="<?= e(
                                $transaction['category']
                                ?? ''
                            ); ?>"
                            placeholder="Fuel"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Type
                        </span>

                        <select
                            name="transaction_type"
                            required
                        >

                            <?php
                            $types = [
                                'expense' => 'Expense',
                                'income' => 'Income',
                                'transfer_out' => 'Transfer Out',
                                'transfer_in' => 'Transfer In',
                            ];
                            ?>

                            <?php foreach (
                                $types as $value => $label
                            ): ?>

                                <option
                                    value="<?= e($value); ?>"
                                    <?= (
                                        $transaction[
                                            'transaction_type'
                                        ]
                                        ?? 'expense'
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
                            Amount
                        </span>

                        <input
                            type="number"
                            name="amount"
                            step="0.01"
                            min="0.01"
                            value="<?= e(
                                isset(
                                    $transaction['amount']
                                )
                                    ? number_format(
                                        abs(
                                            (float) $transaction[
                                                'amount'
                                            ]
                                        ),
                                        2,
                                        '.',
                                        ''
                                    )
                                    : ''
                            ); ?>"
                            placeholder="0.00"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Status
                        </span>

                        <select
                            name="transaction_status"
                        >

                            <?php
                            $statuses = [
                                'posted' => 'Posted',
                                'pending' => 'Pending',
                            ];
                            ?>

                            <?php foreach (
                                $statuses as $value => $label
                            ): ?>

                                <option
                                    value="<?= e($value); ?>"
                                    <?= (
                                        $transaction[
                                            'transaction_status'
                                        ]
                                        ?? 'posted'
                                    ) === $value
                                        ? 'selected'
                                        : ''; ?>
                                >
                                    <?= e($label); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </label>

                </div>


                <label class="transaction-notes-field">

                    <span>
                        Notes
                    </span>

                    <textarea
                        name="notes"
                        rows="4"
                        placeholder="Optional notes..."
                    ><?= e(
                        $transaction['notes']
                        ?? ''
                    ); ?></textarea>

                </label>


                <label class="settings-toggle-row">

                    <div>

                        <strong>
                            Recurring transaction
                        </strong>

                        <small>
                            Mark regular bills, deposits,
                            subscriptions, or other repeating activity.
                        </small>

                    </div>

                    <input
                        type="checkbox"
                        name="is_recurring"
                        value="1"
                        <?= !empty(
                            $transaction['is_recurring']
                        )
                            ? 'checked'
                            : ''; ?>
                    >

                </label>


                <?php if ($transactionId > 0): ?>

                    <div class="save-rule-box">

                        <label class="settings-toggle-row">

                            <div>

                                <strong>
                                    Remember this correction
                                </strong>

                                <small>
                                    Create a rule so future matching
                                    transactions use this name and category.
                                </small>

                            </div>

                            <input
                                type="checkbox"
                                name="save_rule"
                                value="1"
                            >

                        </label>


                        <label class="rule-match-field">

                            <span>
                                Match future descriptions containing
                            </span>

                            <input
                                type="text"
                                name="rule_match_value"
                                value="<?= e(
                                    $transaction[
                                        'raw_description'
                                    ]
                                    ?: $transaction[
                                        'description'
                                    ]
                                ); ?>"
                            >

                        </label>

                    </div>

                <?php endif; ?>


                <div class="account-form-actions">

                    <a
                        class="secondary-button"
                        href="<?= e(
                            url('transactions.php')
                        ); ?>"
                    >
                        Cancel
                    </a>

                    <button
                        class="primary-button"
                        type="submit"
                    >
                        <?= $transactionId > 0
                            ? 'Save Changes'
                            : 'Add Transaction'; ?>
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

