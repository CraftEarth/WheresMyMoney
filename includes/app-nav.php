<aside class="app-sidebar">

    <div class="app-brand">

        <span class="app-brand-mark">$</span>

        <div>
            <strong>Where's My Money?</strong>
            <small>One clear picture.</small>
        </div>

    </div>


    <nav class="app-nav">

        <a
            class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : ''; ?>"
            href="<?= e(url('dashboard.php')); ?>"
        >
            <span>D</span>
            Dashboard
        </a>

        <a
            class="<?= ($activePage ?? '') === 'accounts' ? 'active' : ''; ?>"
            href="<?= e(url('accounts.php')); ?>"
        >
            <span>A</span>
            Accounts
        </a>

        <a
            class="<?= ($activePage ?? '') === 'transactions' ? 'active' : ''; ?>"
            href="<?= e(url('transactions.php')); ?>"
        >
            <span>T</span>
            Transactions
        </a>

        <a
            class="<?= ($activePage ?? '') === 'bills' ? 'active' : ''; ?>"
            href="<?= e(url('bills.php')); ?>"
        >
            <span>B</span>
            Bills
        </a>

        <a
            class="<?= ($activePage ?? '') === 'budgets' ? 'active' : ''; ?>"
            href="<?= e(url('budgets.php')); ?>"
        >
            <span>$</span>
            Budgets
        </a>

        <a
            class="<?= ($activePage ?? '') === 'crypto' ? 'active' : ''; ?>"
            href="<?= e(url('crypto.php')); ?>"
        >
            <span>C</span>
            Crypto
        </a>

        <a
            class="<?= ($activePage ?? '') === 'reports' ? 'active' : ''; ?>"
            href="<?= e(url('reports.php')); ?>"
        >
            <span>R</span>
            Reports
        </a>

        <a
            class="<?= ($activePage ?? '') === 'settings' ? 'active' : ''; ?>"
            href="<?= e(url('settings.php')); ?>"
        >
            <span>S</span>
            Settings
        </a>

    </nav>


    <div class="connect-card">

        <strong>
            Connect your money
        </strong>

        <p>
            Banks, cards, wallets, cash,
            crypto, and investments.
        </p>

        <button type="button">
            + Link Account
        </button>

    </div>

</aside>
