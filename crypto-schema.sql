CREATE TABLE IF NOT EXISTS crypto_wallets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    wallet_address VARCHAR(100) NOT NULL,
    wallet_label VARCHAR(120) NOT NULL DEFAULT 'MetaMask',
    provider VARCHAR(40) NOT NULL DEFAULT 'alchemy',
    network VARCHAR(40) NOT NULL DEFAULT 'eth-mainnet',
    wallet_status VARCHAR(20) NOT NULL DEFAULT 'active',
    last_synced_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uniq_user_wallet_network (
        user_id,
        wallet_address,
        network
    ),

    INDEX idx_crypto_wallets_user (
        user_id
    )
);

CREATE TABLE IF NOT EXISTS crypto_assets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    wallet_id BIGINT UNSIGNED NOT NULL,
    network VARCHAR(40) NOT NULL,
    asset_type VARCHAR(30) NOT NULL,
    contract_address VARCHAR(100) NULL,
    symbol VARCHAR(40) NULL,
    asset_name VARCHAR(160) NULL,
    quantity DECIMAL(36,18) NOT NULL DEFAULT 0,
    current_price DECIMAL(20,8) NULL,
    current_value DECIMAL(20,2) NOT NULL DEFAULT 0,
    external_asset_id VARCHAR(255) NULL,
    is_archived TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_crypto_assets_user_wallet (
        user_id,
        wallet_id
    )
);

CREATE TABLE IF NOT EXISTS crypto_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    wallet_id BIGINT UNSIGNED NOT NULL,
    network VARCHAR(40) NOT NULL,
    transaction_hash VARCHAR(100) NOT NULL,
    block_timestamp DATETIME NULL,
    from_address VARCHAR(100) NULL,
    to_address VARCHAR(100) NULL,
    asset_symbol VARCHAR(40) NULL,
    contract_address VARCHAR(100) NULL,
    quantity DECIMAL(36,18) NULL,
    usd_value DECIMAL(20,2) NULL,
    direction VARCHAR(20) NOT NULL DEFAULT 'unknown',
    transaction_type VARCHAR(40) NOT NULL DEFAULT 'transfer',
    category VARCHAR(80) NOT NULL DEFAULT 'Crypto Transfer',
    gas_native DECIMAL(36,18) NULL,
    gas_usd DECIMAL(20,2) NULL,
    needs_review TINYINT(1) NOT NULL DEFAULT 0,
    is_archived TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uniq_crypto_tx (
        user_id,
        network,
        transaction_hash,
        wallet_id
    ),

    INDEX idx_crypto_tx_user_wallet (
        user_id,
        wallet_id
    )
);
