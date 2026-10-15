<div class="app-layout">

    <main class="app-main">

        <header class="settings-header">

            <div>

                <p class="app-greeting">
                    Personal preferences
                </p>

                <h1>
                    Settings
                </h1>

                <p class="settings-subtitle">
                    Control how your financial dashboard behaves.
                </p>

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


        <form method="post">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()); ?>"
            >


            <section class="settings-layout">

                <div class="settings-main">

                    <article class="settings-card">

                        <div class="settings-card-heading">

                            <div>
                                <small>Profile</small>
                                <h2>Personal Information</h2>
                            </div>

                        </div>


                        <div class="settings-form-grid">

                            <label>

                                <span>
                                    Display Name
                                </span>

                                <input
                                    type="text"
                                    name="display_name"
                                    value="<?= e(
                                        $user['display_name'] ?? ''
                                    ); ?>"
                                    required
                                >

                            </label>


                            <label>

                                <span>
                                    Email
                                </span>

                                <input
                                    type="email"
                                    value="<?= e(
                                        $user['email'] ?? ''
                                    ); ?>"
                                    disabled
                                >

                            </label>


                            <label>

                                <span>
                                    Currency
                                </span>

                                <select name="currency">

                                    <?php foreach (
                                        ['USD', 'CAD', 'EUR', 'GBP']
                                        as $currency
                                    ): ?>

                                        <option
                                            value="<?= e($currency); ?>"
                                            <?= (
                                                $preferences['currency']
                                                ?? 'USD'
                                            ) === $currency
                                                ? 'selected'
                                                : ''; ?>
                                        >
                                            <?= e($currency); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </label>


                            <label>

                                <span>
                                    Starting Page
                                </span>

                                <select name="starting_page">

                                    <?php
                                    $pages = [
                                        'dashboard' => 'Dashboard',
                                        'accounts' => 'Accounts',
                                        'transactions' => 'Transactions',
                                        'reports' => 'Reports',
                                    ];
                                    ?>

                                    <?php foreach (
                                        $pages as $value => $label
                                    ): ?>

                                        <option
                                            value="<?= e($value); ?>"
                                            <?= (
                                                $preferences['starting_page']
                                                ?? 'dashboard'
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

                    </article>


                    <article class="settings-card">

                        <div class="settings-card-heading">

                            <div>
                                <small>Money calculations</small>
                                <h2>Financial Preferences</h2>
                            </div>

                        </div>


                        <div class="settings-toggle-list">

                            <?php
                            $toggles = [
                                'include_crypto_net_worth' =>
                                    'Include crypto in net worth',

                                'include_investments_net_worth' =>
                                    'Include investments in net worth',

                                'include_crypto_safe_to_spend' =>
                                    'Include crypto in safe to spend',

                                'show_habit_spending' =>
                                    'Show habit spending',
                            ];
                            ?>


                            <?php foreach (
                                $toggles as $name => $label
                            ): ?>

                                <label class="settings-toggle-row">

                                    <div>
                                        <strong>
                                            <?= e($label); ?>
                                        </strong>
                                    </div>

                                    <input
                                        type="checkbox"
                                        name="<?= e($name); ?>"
                                        value="1"
                                        <?= !empty(
                                            $preferences[$name]
                                        )
                                            ? 'checked'
                                            : ''; ?>
                                    >

                                </label>

                            <?php endforeach; ?>

                        </div>

                    </article>

                </div>


                <aside class="settings-side">

                    <article class="settings-card">

                        <div class="settings-card-heading">

                            <div>
                                <small>Appearance</small>
                                <h2>Interface</h2>
                            </div>

                        </div>


                        <label class="settings-toggle-row">

                            <div>

                                <strong>
                                    Dark Mode
                                </strong>

                                <small>
                                    Stored now. Visual theme
                                    comes later.
                                </small>

                            </div>

                            <input
                                type="checkbox"
                                name="dark_mode"
                                value="1"
                                <?= !empty(
                                    $preferences['dark_mode']
                                )
                                    ? 'checked'
                                    : ''; ?>
                            >

                        </label>

                    </article>


                    <article class="settings-card">

                        <div class="settings-card-heading">

                            <div>
                                <small>Account</small>
                                <h2>Session</h2>
                            </div>

                        </div>

                        <a
                            class="settings-logout"
                            href="<?= e(url('logout.php')); ?>"
                        >
                            Logout
                        </a>

                    </article>


                    <button
                        class="settings-save-button"
                        type="submit"
                    >
                        Save Settings
                    </button>

                </aside>

            </section>

        </form>

    </main>

</div>
