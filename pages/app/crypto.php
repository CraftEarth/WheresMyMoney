<div class="app-layout">

    <main class="app-main">

        <header class="crypto-header">

            <div>

                <p class="app-greeting">
                    Digital assets
                </p>

                <h1>
                    Crypto
                </h1>

                <p class="crypto-subtitle">
                    Track exchange balances, public wallets,
                    and manually entered holdings.
                </p>

            </div>

            <div class="crypto-actions">

                <button
                    class="secondary-button"
                    type="button"
                >
                    Add Wallet
                </button>

                <button
                    class="primary-button"
                    type="button"
                >
                    + Connect Exchange
                </button>

            </div>

        </header>


        <section class="crypto-summary-grid">

            <article>

                <span>
                    Portfolio Value
                </span>

                <strong>
                    <?= money($totalValue); ?>
                </strong>

                <small class="<?= $totalChangeValue >= 0
                    ? 'positive'
                    : 'negative'; ?>"
                >
                    <?= $totalChangeValue >= 0 ? '+' : '-'; ?>
                    <?= money(abs($totalChangeValue)); ?>
                    today
                </small>

            </article>


            <article>

                <span>
                    24h Change
                </span>

                <strong class="<?= $totalChangePercent >= 0
                    ? 'positive'
                    : 'negative'; ?>"
                >
                    <?= $totalChangePercent >= 0 ? '+' : '-'; ?>
                    <?= number_format(
                        abs($totalChangePercent),
                        2
                    ); ?>%
                </strong>

                <small>
                    Across tracked holdings
                </small>

            </article>


            <article>

                <span>
                    Assets
                </span>

                <strong>
                    <?= count($holdings); ?>
                </strong>

                <small>
                    Tracked coins and tokens
                </small>

            </article>


            <article>

                <span>
                    Sources
                </span>

                <strong>
                    3
                </strong>

                <small>
                    Exchanges and wallets
                </small>

            </article>

        </section>


        <section class="crypto-layout">

            <div class="crypto-card">

                <div class="crypto-card-heading">

                    <div>

                        <small>
                            Holdings
                        </small>

                        <h2>
                            Crypto Portfolio
                        </h2>

                    </div>

                    <div class="crypto-filter">
                        Current Value
                    </div>

                </div>


                <div class="crypto-table">

                    <div class="crypto-table-head">

                        <span>Asset</span>
                        <span>Quantity</span>
                        <span>Price</span>
                        <span>24h</span>
                        <span>Source</span>
                        <span>Value</span>

                    </div>


                    <?php foreach ($holdings as $holding): ?>

                        <article class="crypto-table-row">

                            <div class="crypto-asset-cell">

                                <span class="crypto-symbol">
                                    <?= e($holding['symbol']); ?>
                                </span>

                                <div>

                                    <strong>
                                        <?= e($holding['name']); ?>
                                    </strong>

                                    <small>
                                        <?= e($holding['network']); ?>
                                    </small>

                                </div>

                            </div>


                            <div>
                                <?= number_format(
                                    (float) $holding['quantity'],
                                    4
                                ); ?>
                            </div>


                            <div>
                                <?= money(
                                    (float) $holding['price']
                                ); ?>
                            </div>


                            <div
                                class="<?= $holding['change'] >= 0
                                    ? 'positive'
                                    : 'negative'; ?>"
                            >
                                <?= $holding['change'] >= 0
                                    ? '+'
                                    : '-'; ?>

                                <?= number_format(
                                    abs(
                                        (float) $holding['change']
                                    ),
                                    2
                                ); ?>%
                            </div>


                            <div>

                                <span class="crypto-source-badge">
                                    <?= e($holding['source']); ?>
                                </span>

                            </div>


                            <div class="crypto-value-cell">

                                <strong>
                                    <?= money(
                                        (float) $holding['value']
                                    ); ?>
                                </strong>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </div>


            <aside class="crypto-side-card">

                <small>
                    Portfolio mix
                </small>

                <h2>
                    Asset Breakdown
                </h2>

                <div class="crypto-allocation-list">

                    <?php foreach ($holdings as $holding): ?>

                        <?php
                        $allocation =
                            $totalValue > 0
                                ? (
                                    $holding['value']
                                    / $totalValue
                                ) * 100
                                : 0;
                        ?>

                        <div class="crypto-allocation-row">

                            <div>

                                <span>
                                    <?= e($holding['symbol']); ?>
                                </span>

                                <strong>
                                    <?= number_format(
                                        $allocation,
                                        1
                                    ); ?>%
                                </strong>

                            </div>

                            <div class="crypto-allocation-track">

                                <span
                                    style="width: <?= (int) round($allocation); ?>%;"
                                ></span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>


                <div class="crypto-note">

                    <strong>
                        Read-only tracking
                    </strong>

                    <p>
                        Public wallet addresses and supported
                        exchange connections should never require
                        seed phrases or private keys.
                    </p>

                </div>

            </aside>

        </section>

    </main>

</div>
