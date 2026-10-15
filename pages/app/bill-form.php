<div class="app-layout">

    <main class="app-main">

        <header class="bills-header">

            <div>

                <p class="app-greeting">
                    <?= $billId > 0
                        ? 'Update bill'
                        : 'Add recurring obligation'; ?>
                </p>

                <h1>
                    <?= $billId > 0
                        ? 'Edit Bill'
                        : 'Add Bill'; ?>
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
                            Bill Name
                        </span>

                        <input
                            type="text"
                            name="bill_name"
                            value="<?= e(
                                $bill['bill_name']
                                ?? ''
                            ); ?>"
                            placeholder="Truck Payment"
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
                                $bill['category']
                                ?? ''
                            ); ?>"
                            placeholder="Auto"
                        >

                    </label>


                    <label>

                        <span>
                            Expected Amount
                        </span>

                        <input
                            type="number"
                            name="expected_amount"
                            step="0.01"
                            min="0.01"
                            value="<?= e(
                                isset($bill['expected_amount'])
                                    ? (string) $bill[
                                        'expected_amount'
                                    ]
                                    : ''
                            ); ?>"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Next Due Date
                        </span>

                        <input
                            type="date"
                            name="next_due_date"
                            value="<?= e(
                                $bill['next_due_date']
                                ?? date('Y-m-d')
                            ); ?>"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Frequency
                        </span>

                        <select name="frequency">

                            <?php
                            $frequencies = [
                                'weekly' => 'Weekly',
                                'biweekly' => 'Biweekly',
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'yearly' => 'Yearly',
                                'one_time' => 'One Time',
                            ];
                            ?>

                            <?php foreach (
                                $frequencies as $value => $label
                            ): ?>

                                <option
                                    value="<?= e($value); ?>"
                                    <?= (
                                        $bill['frequency']
                                        ?? 'monthly'
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
                            Paying Account
                        </span>

                        <select name="account_id">

                            <option value="">
                                Not Assigned
                            </option>

                            <?php foreach (
                                $accounts as $accountOption
                            ): ?>

                                <option
                                    value="<?= (int) $accountOption['id']; ?>"
                                    <?= (
                                        (int) (
                                            $bill['account_id']
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
                            Status
                        </span>

                        <select name="bill_status">

                            <?php
                            $statuses = [
                                'upcoming' => 'Upcoming',
                                'due_soon' => 'Due Soon',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                            ];
                            ?>

                            <?php foreach (
                                $statuses as $value => $label
                            ): ?>

                                <option
                                    value="<?= e($value); ?>"
                                    <?= (
                                        $bill['bill_status']
                                        ?? 'upcoming'
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


                <label class="settings-toggle-row">

                    <div>

                        <strong>
                            Autopay
                        </strong>

                        <small>
                            Mark this bill as automatically paid
                            by the assigned account.
                        </small>

                    </div>

                    <input
                        type="checkbox"
                        name="autopay"
                        value="1"
                        <?= !empty($bill['autopay'])
                            ? 'checked'
                            : ''; ?>
                    >

                </label>


                <label class="transaction-notes-field">

                    <span>
                        Notes
                    </span>

                    <textarea
                        name="notes"
                        rows="4"
                    ><?= e(
                        $bill['notes']
                        ?? ''
                    ); ?></textarea>

                </label>


                <div class="account-form-actions">

                    <a
                        class="secondary-button"
                        href="<?= e(url('bills.php')); ?>"
                    >
                        Cancel
                    </a>

                    <button
                        class="primary-button"
                        type="submit"
                    >
                        <?= $billId > 0
                            ? 'Save Changes'
                            : 'Add Bill'; ?>
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>
