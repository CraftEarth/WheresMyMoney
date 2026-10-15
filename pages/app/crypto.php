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
                    Track manually entered holdings,
                    wallets, and exchange balances.
                </p>

            </div>

            <div class="crypto-actions">

                <a
                    class="primary-button"
                    href="<?= e(
                        url('crypto-form.php')
                    ); ?>"
                >
                    + Add Holding
                </a>

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

                <small>
                    Current tracked value
                </small>

            </article>


            <article>

                <span>
                    Cost Basis
                </span>

                <strong>
                    <?= money($totalCostBasis); ?>
                </strong>

                <small>
                    From entered average cost
                </small>

            </article>


            <article>

                <span>
                    Gain / Loss
                </span>

                <strong class="<?= $totalGainLoss >= 0
                    ? 'positive'
                    : 'negative'; ?>"
                >
                    <?= $totalGainLoss >= 0
                        ? '+'
                        : '-'; ?>

                    <?= money(
                        abs($totalGainLoss)
                    ); ?>
                </strong>

                <small>
                    Tracked unrealized position
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
                    Active holdings
                </small>

            </article>

        </section>


        <section class="crypto-card">

            <div class="crypto-card-heading">

                <div>

                    <small>
                        Holdings
                    </small>

                    <h2>
                        Crypto Portfolio
                    </h2>

                </div>

            </div>


            <?php if ($holdings === []): ?>

                <div class="accounts-empty">

                    <strong>
                        No crypto holdings yet.
                    </strong>

                    <p>
                        Add a manual holding to begin
                        tracking its value.
                    </p>

                    <a
                        class="primary-button"
                        href="<?= e(
                            url('crypto-form.php')
                        ); ?>"
                    >
                        Add Holding
                    </a>

                </div>

            <?php else: ?>

                <div class="crypto-table">

                    <div class="crypto-table-head">

                        <span>Asset</span>
                        <span>Quantity</span>
                        <span>Price</span>
                        <span>Source</span>
                        <span>Value</span>
                        <span>Gain / Loss</span>
                        <span>Actions</span>

                    </div>


                    <?php foreach (
                        $holdings as $holding
                    ): ?>

                        <article class="crypto-table-row">

                            <div class="crypto-asset-cell">

                                <span class="crypto-symbol">
                                    <?= e(
                                        $holding[
                                            'asset_symbol'
                                        ]
                                    ); ?>
                                </span>

                                <div>

                                    <strong>
                                        <?= e(
                                            $holding[
                                                'asset_name'
                                            ]
                                        ); ?>
                                    </strong>

                                    <small>
                                        <?= e(
                                            $holding['network']
                                            ?? 'Manual'
                                        ); ?>
                                    </small>

                                </div>

                            </div>


                            <div>
                                <?= number_format(
                                    (float) $holding[
                                        'quantity'
                                    ],
                                    6
                                ); ?>
                            </div>


                            <div>
                                <?= money(
                                    (float) $holding[
                                        'current_price'
                                    ]
                                ); ?>
                            </div>


                            <div>
                                <?= e(
                                    $holding['source_name']
                                    ?? 'Manual'
                                ); ?>
                            </div>


                            <div class="crypto-value-cell">

                                <strong>
                                    <?= money(
                                        (float) $holding[
                                            'value'
                                        ]
                                    ); ?>
                                </strong>

                            </div>


                            <div>

                                <?php if (
                                    $holding['gain_loss']
                                    !== null
                                ): ?>

                                    <strong class="<?= (
                                        (float) $holding[
                                            'gain_loss'
                                        ] >= 0
                                    )
                                        ? 'positive'
                                        : 'negative'; ?>"
                                    >
                                        <?= (
                                            (float) $holding[
                                                'gain_loss'
                                            ] >= 0
                                        )
                                            ? '+'
                                            : '-'; ?>

                                        <?= money(
                                            abs(
                                                (float) $holding[
                                                    'gain_loss'
                                                ]
                                            )
                                        ); ?>
                                    </strong>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </div>


                            <div class="crypto-actions-cell">

                                <a
                                    href="<?= e(
                                        url(
                                            'crypto-form.php?id='
                                            . $holding['id']
                                        )
                                    ); ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    method="post"
                                    action="<?= e(
                                        url(
                                            'crypto-archive.php'
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
                                        name="holding_id"
                                        value="<?= (int) $holding[
                                            'id'
                                        ]; ?>"
                                    >

                                    <button type="submit">
                                        Archive
                                    </button>

                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>
