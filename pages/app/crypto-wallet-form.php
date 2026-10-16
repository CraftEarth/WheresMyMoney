<div class="app-layout">

    <main class="app-main">

        <header class="crypto-header">

            <div>

                <p class="app-greeting">
                    Wallet connection
                </p>

                <h1>
                    Add Crypto Wallet
                </h1>

                <p class="crypto-subtitle">
                    Connect public wallet addresses only.
                    Private keys and recovery phrases are never required.
                </p>

            </div>

        </header>


        <section class="crypto-card">

            <div class="crypto-card-heading">

                <div>

                    <small>
                        Provider
                    </small>

                    <h2>
                        Choose Wallet
                    </h2>

                </div>

            </div>


            <div
                style="
                    display:flex;
                    gap:10px;
                    margin-bottom:22px;
                "
            >

                <a
                    class="<?= $walletType === 'metamask'
                        ? 'primary-button'
                        : 'secondary-button'; ?>"
                    href="<?= e(
                        url(
                            'crypto-wallet-form.php?type=metamask'
                        )
                    ); ?>"
                >
                    MetaMask
                </a>

                <a
                    class="<?= $walletType === 'phantom'
                        ? 'primary-button'
                        : 'secondary-button'; ?>"
                    href="<?= e(
                        url(
                            'crypto-wallet-form.php?type=phantom'
                        )
                    ); ?>"
                >
                    Phantom
                </a>

            </div>


            <?php if ($errors !== []): ?>

                <div class="form-errors">

                    <?php foreach ($errors as $error): ?>

                        <p>
                            <?= e($error); ?>
                        </p>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <form
                method="post"
                action="<?= e(
                    url(
                        'crypto-wallet-form.php'
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
                    name="wallet_type"
                    value="<?= e(
                        $walletType
                    ); ?>"
                >


                <div class="form-grid">


                    <label>

                        <span>
                            Wallet Label
                        </span>

                        <input
                            type="text"
                            name="wallet_label"
                            value="<?= e(
                                $walletLabel
                            ); ?>"
                            required
                        >

                    </label>


                    <?php if (
                        $walletType === 'phantom'
                    ): ?>

                        <div
                            style="
                                grid-column:1 / -1;
                                padding:12px 0;
                            "
                        >

                            <strong>
                                Phantom addresses
                            </strong>

                            <p
                                style="
                                    margin:5px 0 0;
                                    opacity:.72;
                                "
                            >
                                Phantom can hold assets on
                                Solana and EVM networks.
                                Add both public receive addresses
                                when available.
                            </p>

                        </div>

                    <?php endif; ?>


                    <label>

                        <span>
                            EVM / Ethereum Address
                        </span>

                        <input
                            type="text"
                            name="evm_address"
                            value="<?= e(
                                $evmAddress
                            ); ?>"
                            placeholder="0x..."
                            autocomplete="off"
                            spellcheck="false"
                        >

                        <small>
                            Used for Ethereum and other
                            EVM-compatible assets.
                        </small>

                    </label>


                    <label>

                        <span>
                            Solana Address
                        </span>

                        <input
                            type="text"
                            name="solana_address"
                            value="<?= e(
                                $solanaAddress
                            ); ?>"
                            placeholder="Solana public address"
                            autocomplete="off"
                            spellcheck="false"
                        >

                        <small>
                            Used for SOL and SPL tokens.
                        </small>

                    </label>


                    <label>

                        <span>
                            Bitcoin Address
                        </span>

                        <input
                            type="text"
                            name="bitcoin_address"
                            value="<?= e(
                                $bitcoinAddress
                            ); ?>"
                            placeholder="Optional"
                            autocomplete="off"
                            spellcheck="false"
                        >

                        <small>
                            Optional. Bitcoin sync will be
                            added separately.
                        </small>

                    </label>

                </div>


                <div
                    style="
                        display:flex;
                        gap:10px;
                        margin-top:24px;
                    "
                >

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Save <?= $walletType === 'phantom'
                            ? 'Phantom'
                            : 'MetaMask'; ?>
                    </button>


                    <a
                        class="secondary-button"
                        href="<?= e(
                            url('crypto.php')
                        ); ?>"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </section>

    </main>

</div>
