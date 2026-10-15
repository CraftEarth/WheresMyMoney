<div class="app-layout">

    <main class="app-main">

        <header class="transactions-header">

            <div>

                <p class="app-greeting">
                    Bring in transaction history
                </p>

                <h1>
                    Import CSV
                </h1>

                <p class="transactions-subtitle">
                    Import statement history without
                    changing the account's current balance.
                </p>

            </div>

            <div class="transactions-actions">

                <a
                    class="secondary-button"
                    href="<?= e(
                        url('transactions.php')
                    ); ?>"
                >
                    Back to Transactions
                </a>

            </div>

        </header>


        <?php if ($successMessage !== null): ?>

            <div class="settings-message success-message">
                <?= e($successMessage); ?>
            </div>

        <?php endif; ?>


        <?php if ($errorMessage !== null): ?>

            <div class="settings-message error-message">
                <?= e($errorMessage); ?>
            </div>

        <?php endif; ?>


        <section class="import-card">

            <div class="import-heading">

                <small>
                    Step 1
                </small>

                <h2>
                    Choose Account & CSV
                </h2>

            </div>


            <form
                method="post"
                enctype="multipart/form-data"
                class="import-upload-form"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()); ?>"
                >

                <input
                    type="hidden"
                    name="action"
                    value="preview"
                >


                <label>

                    <span>
                        Import Into Account
                    </span>

                    <select
                        name="account_id"
                        required
                    >

                        <option value="">
                            Choose account
                        </option>

                        <?php foreach (
                            $accounts as $account
                        ): ?>

                            <option
                                value="<?= (int) $account['id']; ?>"
                            >
                                <?= e(
                                    $account[
                                        'account_name'
                                    ]
                                ); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>


                <label>

                    <span>
                        CSV File
                    </span>

                    <input
                        type="file"
                        name="csv_file"
                        accept=".csv,text/csv"
                        required
                    >

                </label>


                <button
                    class="primary-button"
                    type="submit"
                >
                    Preview CSV
                </button>

            </form>

        </section>


        <?php if ($preview !== null): ?>

            <section class="import-card">

                <div class="import-heading">

                    <small>
                        Step 2
                    </small>

                    <h2>
                        Map Columns
                    </h2>

                    <p>
                        <?= e(
                            $preview[
                                'filename'
                            ]
                        ); ?>

                        &mdash;

                        <?= count(
                            $preview['rows']
                        ); ?>
                        rows

                        &mdash;

                        <?= e(
                            $preview[
                                'account_name'
                            ]
                        ); ?>
                    </p>

                </div>


                <form method="post">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(
                            csrf_token()
                        ); ?>"
                    >

                    <input
                        type="hidden"
                        name="action"
                        value="import"
                    >


                    <div class="import-map-grid">

                        <label>

                            <span>
                                Date Column
                            </span>

                            <select
                                name="date_column"
                                required
                            >

                                <?php foreach (
                                    $preview[
                                        'headers'
                                    ]
                                    as $index => $header
                                ): ?>

                                    <option
                                        value="<?= (int) $index; ?>"
                                        <?= $dateGuess === $index
                                            ? 'selected'
                                            : ''; ?>
                                    >
                                        <?= e(
                                            (string) $header
                                        ); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </label>


                        <label>

                            <span>
                                Description Column
                            </span>

                            <select
                                name="description_column"
                                required
                            >

                                <?php foreach (
                                    $preview[
                                        'headers'
                                    ]
                                    as $index => $header
                                ): ?>

                                    <option
                                        value="<?= (int) $index; ?>"
                                        <?= $descriptionGuess === $index
                                            ? 'selected'
                                            : ''; ?>
                                    >
                                        <?= e(
                                            (string) $header
                                        ); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </label>


                        <label>

                            <span>
                                Amount Column
                            </span>

                            <select
                                name="amount_column"
                                required
                            >

                                <?php foreach (
                                    $preview[
                                        'headers'
                                    ]
                                    as $index => $header
                                ): ?>

                                    <option
                                        value="<?= (int) $index; ?>"
                                        <?= $amountGuess === $index
                                            ? 'selected'
                                            : ''; ?>
                                    >
                                        <?= e(
                                            (string) $header
                                        ); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </label>


                        <label>

                            <span>
                                Category Column
                            </span>

                            <select
                                name="category_column"
                            >

                                <option value="-1">
                                    None / Uncategorized
                                </option>

                                <?php foreach (
                                    $preview[
                                        'headers'
                                    ]
                                    as $index => $header
                                ): ?>

                                    <option
                                        value="<?= (int) $index; ?>"
                                        <?= $categoryGuess === $index
                                            ? 'selected'
                                            : ''; ?>
                                    >
                                        <?= e(
                                            (string) $header
                                        ); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </label>


                        <label>

                            <span>
                                Amount Sign Format
                            </span>

                            <select name="sign_mode">

                                <option
                                    value="negative_expense"
                                >
                                    Negative = Expense
                                </option>

                                <option
                                    value="positive_expense"
                                >
                                    Positive = Expense
                                </option>

                            </select>

                        </label>

                    </div>


                    <div class="import-preview-table">

                        <div class="import-preview-head">

                            <?php foreach (
                                $preview['headers']
                                as $header
                            ): ?>

                                <span>
                                    <?= e(
                                        (string) $header
                                    ); ?>
                                </span>

                            <?php endforeach; ?>

                        </div>


                        <?php foreach (
                            array_slice(
                                $preview['rows'],
                                0,
                                8
                            )
                            as $row
                        ): ?>

                            <div class="import-preview-row">

                                <?php foreach (
                                    $preview['headers']
                                    as $index => $header
                                ): ?>

                                    <span>
                                        <?= e(
                                            (string) (
                                                $row[$index]
                                                ?? ''
                                            )
                                        ); ?>
                                    </span>

                                <?php endforeach; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>


                    <div class="import-warning">

                        <strong>
                            Balance protection
                        </strong>

                        <p>
                            Imported historical transactions
                            do not modify the account's current
                            balance. They affect spending,
                            income, budgets, and reports only.
                        </p>

                    </div>


                    <div class="account-form-actions">

                        <button
                            class="primary-button"
                            type="submit"
                        >
                            Import Transactions
                        </button>

                    </div>

                </form>

            </section>

        <?php endif; ?>

    </main>

</div>
