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
                    class="secondary-button"
                    href="<?= e(
                        url('crypto-wallet-form.php')
                    ); ?>"
                >
                    + Add Wallet
                </a>

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
                    Known cost basis only
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
                    Known cost basis positions only
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



        <!-- CONNECTED WALLETS SECTION -->

        <?php

        $walletStmt =
            db()->prepare(
                '
                SELECT *
                FROM crypto_wallet_groups
                WHERE user_id = :user_id
                  AND wallet_status = "active"
                ORDER BY id ASC
                '
            );

        $walletStmt->execute([
            'user_id' =>
                current_user_id(),
        ]);

        $connectedWallets =
            $walletStmt->fetchAll();

        ?>


        <section class="crypto-card">

            <div class="crypto-card-heading">

                <div>

                    <small>
                        Wallets
                    </small>

                    <h2>
                        Connected Wallets
                    </h2>

                </div>

                <a
                    class="secondary-button"
                    href="<?= e(
                        url(
                            'crypto-wallet-form.php'
                        )
                    ); ?>"
                >
                    + Add Wallet
                </a>

            </div>


            <?php if (
                $connectedWallets === []
            ): ?>

                <p>
                    No connected wallets yet.
                </p>

            <?php else: ?>

                <?php foreach (
                    $connectedWallets
                    as $wallet
                ): ?>

                    <?php

                    $addressStmt =
                        db()->prepare(
                            '
                            SELECT *
                            FROM crypto_wallet_addresses
                            WHERE user_id = :user_id
                              AND wallet_group_id = :wallet_id
                              AND address_status = "active"
                            ORDER BY id ASC
                            '
                        );

                    $addressStmt->execute([
                        'user_id' =>
                            current_user_id(),

                        'wallet_id' =>
                            $wallet['id'],
                    ]);

                    $walletAddresses =
                        $addressStmt->fetchAll();

                    ?>


                    <div
                        style="
                            border-top:1px solid #e5e7eb;
                            padding:14px 0;
                        "
                    >

                        <strong>
                            <?= e(
                                $wallet[
                                    'wallet_label'
                                ]
                            ); ?>
                        </strong>

                        <span>
                            (
                            <?= e(
                                ucfirst(
                                    $wallet[
                                        'wallet_type'
                                    ]
                                )
                            ); ?>
                            )
                        </span>


                        <?php foreach (
                            $walletAddresses
                            as $address
                        ): ?>

                            <div
                                style="
                                    margin-top:6px;
                                    font-size:13px;
                                "
                            >

                                <strong>
                                    <?= e(
                                        strtoupper(
                                            $address[
                                                'address_type'
                                            ]
                                        )
                                    ); ?>
                                </strong>

                                &nbsp;

                                <?= e(
                                    substr(
                                        $address[
                                            'wallet_address'
                                        ],
                                        0,
                                        12
                                    )
                                ); ?>

                                ...

                                <?= e(
                                    substr(
                                        $address[
                                            'wallet_address'
                                        ],
                                        -8
                                    )
                                ); ?>

                            </div>

                        <?php endforeach; ?>


                        <div
                            style="
                                margin-top:8px;
                                display:flex;
                                align-items:center;
                                gap:10px;
                            "
                        >

                            <a
                                class="primary-button"
                                href="<?= e(
                                    url(
                                        'crypto-wallet-sync.php?wallet_id='
                                        . $wallet['id']
                                    )
                                ); ?>"
                            >
                                Sync Wallet
                            </a>

                            <small>
                                <?= !empty(
                                    $wallet[
                                        'last_synced_at'
                                    ]
                                )
                                    ? 'Last synced '
                                        . e(
                                            $wallet[
                                                'last_synced_at'
                                            ]
                                        )
                                    : 'Not synced yet'; ?>
                            </small>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

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
                                <span class="crypto-quantity-value"><?= e(
                                    crypto_display_quantity(
                                        (float) $holding[
                                            'quantity'
                                        ]
                                    )
                                ); ?></span>
                            </div>


                            <div>
                                <span class="crypto-price-value"><?= e(
                                    crypto_display_price(
                                        (float) $holding[
                                            'current_price'
                                        ]
                                    )
                                ); ?></span>
                            </div>


                            <div>
                                <?= e(
                                    $holding['source_name']
                                    ?? 'Manual'
                                ); ?>
                            </div>


                            <div class="crypto-value-cell">

                                <strong>
                                    <?= e(
                                        crypto_display_value(
                                            (float) $holding[
                                                'value'
                                            ],
                                            (float) $holding[
                                                'current_price'
                                            ],
                                            (float) $holding[
                                                'quantity'
                                            ]
                                        )
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

                                <?php if (
                                    ($holding['source_type'] ?? 'manual')
                                    === 'manual'
                                ): ?>

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

                                <?php else: ?>

                                    <small>
                                        Synced
                                    </small>

                                <?php endif; ?>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>
