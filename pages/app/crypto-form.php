<div class="app-layout">

    <main class="app-main">

        <header class="crypto-header">

            <div>

                <p class="app-greeting">
                    <?= $holdingId > 0
                        ? 'Update holding'
                        : 'Add digital asset'; ?>
                </p>

                <h1>
                    <?= $holdingId > 0
                        ? 'Edit Crypto Holding'
                        : 'Add Crypto Holding'; ?>
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
                            Symbol
                        </span>

                        <input
                            type="text"
                            name="asset_symbol"
                            value="<?= e(
                                $holding[
                                    'asset_symbol'
                                ]
                                ?? ''
                            ); ?>"
                            placeholder="BTC"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Asset Name
                        </span>

                        <input
                            type="text"
                            name="asset_name"
                            value="<?= e(
                                $holding[
                                    'asset_name'
                                ]
                                ?? ''
                            ); ?>"
                            placeholder="Bitcoin"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Network
                        </span>

                        <input
                            type="text"
                            name="network"
                            value="<?= e(
                                $holding['network']
                                ?? ''
                            ); ?>"
                            placeholder="Bitcoin"
                        >

                    </label>


                    <label>

                        <span>
                            Source
                        </span>

                        <input
                            type="text"
                            name="source_name"
                            value="<?= e(
                                $holding[
                                    'source_name'
                                ]
                                ?? ''
                            ); ?>"
                            placeholder="Coinbase"
                        >

                    </label>


                    <label>

                        <span>
                            Quantity
                        </span>

                        <input
                            type="number"
                            name="quantity"
                            step="0.00000001"
                            min="0"
                            value="<?= e(
                                isset(
                                    $holding['quantity']
                                )
                                    ? (string) $holding[
                                        'quantity'
                                    ]
                                    : ''
                            ); ?>"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Current Price
                        </span>

                        <input
                            type="number"
                            name="current_price"
                            step="0.01"
                            min="0"
                            value="<?= e(
                                isset(
                                    $holding[
                                        'current_price'
                                    ]
                                )
                                    ? (string) $holding[
                                        'current_price'
                                    ]
                                    : ''
                            ); ?>"
                            required
                        >

                    </label>


                    <label>

                        <span>
                            Average Cost
                        </span>

                        <input
                            type="number"
                            name="average_cost"
                            step="0.01"
                            min="0"
                            value="<?= e(
                                isset(
                                    $holding[
                                        'average_cost'
                                    ]
                                )
                                    ? (string) $holding[
                                        'average_cost'
                                    ]
                                    : ''
                            ); ?>"
                            placeholder="Optional"
                        >

                    </label>


                    <label>

                        <span>
                            Public Wallet Address
                        </span>

                        <input
                            type="text"
                            name="wallet_address"
                            value="<?= e(
                                $holding[
                                    'wallet_address'
                                ]
                                ?? ''
                            ); ?>"
                            placeholder="Optional read-only address"
                        >

                    </label>

                </div>


                <div class="account-form-actions">

                    <a
                        class="secondary-button"
                        href="<?= e(
                            url('crypto.php')
                        ); ?>"
                    >
                        Cancel
                    </a>

                    <button
                        class="primary-button"
                        type="submit"
                    >
                        <?= $holdingId > 0
                            ? 'Save Changes'
                            : 'Add Holding'; ?>
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>
