<div class="app-layout">

    <main class="app-main">

        <header class="transactions-header">

            <div>

                <p class="app-greeting">
                    Transaction intelligence
                </p>

                <h1>
                    Transaction Rules
                </h1>

                <p class="transactions-subtitle">
                    Turn raw bank descriptions into
                    clean names and categories automatically.
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


        <section class="rules-layout">

            <article class="rules-card">

                <div class="rules-heading">

                    <small>
                        <?= $ruleId > 0
                            ? 'Edit rule'
                            : 'New rule'; ?>
                    </small>

                    <h2>
                        Description Matching
                    </h2>

                </div>


                <form
                    method="post"
                    class="account-form"
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
                        name="rule_id"
                        value="<?= (int) $ruleId; ?>"
                    >


                    <div class="rules-form-grid">

                        <label>

                            <span>
                                Description Contains
                            </span>

                            <input
                                type="text"
                                name="match_value"
                                value="<?= e(
                                    $rule['match_value']
                                    ?? ''
                                ); ?>"
                                placeholder="SSA TREAS"
                                required
                            >

                        </label>


                        <label>

                            <span>
                                Clean Display Name
                            </span>

                            <input
                                type="text"
                                name="display_name"
                                value="<?= e(
                                    $rule['display_name']
                                    ?? ''
                                ); ?>"
                                placeholder="Social Security Deposit"
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
                                    $rule['category']
                                    ?? ''
                                ); ?>"
                                placeholder="Benefits - Social Security"
                            >

                        </label>


                        <label>

                            <span>
                                Priority
                            </span>

                            <input
                                type="number"
                                name="priority"
                                min="1"
                                value="<?= e(
                                    (string) (
                                        $rule['priority']
                                        ?? 100
                                    )
                                ); ?>"
                            >

                        </label>

                    </div>


                    <label class="settings-toggle-row">

                        <div>

                            <strong>
                                Enabled
                            </strong>

                            <small>
                                Lower priority numbers run first.
                            </small>

                        </div>

                        <input
                            type="checkbox"
                            name="enabled"
                            value="1"
                            <?= !isset($rule)
                                || !array_key_exists(
                                    'enabled',
                                    $rule
                                )
                                || !empty($rule['enabled'])
                                    ? 'checked'
                                    : ''; ?>
                        >

                    </label>


                    <div class="account-form-actions">

                        <?php if ($ruleId > 0): ?>

                            <a
                                class="secondary-button"
                                href="<?= e(
                                    url(
                                        'transaction-rules.php'
                                    )
                                ); ?>"
                            >
                                Cancel
                            </a>

                        <?php endif; ?>

                        <button
                            class="primary-button"
                            type="submit"
                        >
                            <?= $ruleId > 0
                                ? 'Save Rule'
                                : 'Add Rule'; ?>
                        </button>

                    </div>

                </form>

            </article>


            <article class="rules-card">

                <div class="rules-heading">

                    <small>
                        Active rules
                    </small>

                    <h2>
                        Normalization Rules
                    </h2>

                </div>


                <?php if ($rules === []): ?>

                    <div class="dashboard-empty">
                        No rules yet.
                    </div>

                <?php else: ?>

                    <div class="rules-list">

                        <?php foreach ($rules as $item): ?>

                            <div class="rule-row">

                                <div>

                                    <strong>
                                        Contains:
                                        <?= e(
                                            $item[
                                                'match_value'
                                            ]
                                        ); ?>
                                    </strong>

                                    <small>

                                        <?= e(
                                            $item[
                                                'display_name'
                                            ]
                                            ?? 'Keep description'
                                        ); ?>

                                        &mdash;

                                        <?= e(
                                            $item['category']
                                            ?? 'Keep category'
                                        ); ?>

                                    </small>

                                </div>


                                <div class="rule-meta">

                                    <span>
                                        Priority
                                        <?= (int) $item[
                                            'priority'
                                        ]; ?>
                                    </span>

                                    <span class="<?= (
                                        (int) $item['enabled']
                                        === 1
                                    )
                                        ? 'rule-on'
                                        : 'rule-off'; ?>"
                                    >
                                        <?= (
                                            (int) $item['enabled']
                                            === 1
                                        )
                                            ? 'Enabled'
                                            : 'Disabled'; ?>
                                    </span>

                                </div>


                                <div class="rule-actions">

                                    <a
                                        href="<?= e(
                                            url(
                                                'transaction-rules.php?id='
                                                . $item['id']
                                            )
                                        ); ?>"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="post"
                                        action="<?= e(
                                            url(
                                                'transaction-rule-archive.php'
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
                                            name="rule_id"
                                            value="<?= (int) $item[
                                                'id'
                                            ]; ?>"
                                        >

                                        <button type="submit">
                                            Archive
                                        </button>

                                    </form>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </article>

        </section>

    </main>

</div>
