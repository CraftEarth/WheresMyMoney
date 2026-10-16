CREATE TABLE IF NOT EXISTS crypto_wallet_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,

    wallet_type VARCHAR(40) NOT NULL,
    wallet_label VARCHAR(120) NOT NULL,

    wallet_status VARCHAR(20) NOT NULL DEFAULT 'active',

    last_synced_at DATETIME NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_crypto_wallet_groups_user (
        user_id
    )
);


CREATE TABLE IF NOT EXISTS crypto_wallet_addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,
    wallet_group_id BIGINT UNSIGNED NOT NULL,

    address_type VARCHAR(40) NOT NULL,

    wallet_address VARCHAR(160) NOT NULL,

    address_label VARCHAR(120) NULL,

    address_status VARCHAR(20) NOT NULL DEFAULT 'active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uniq_user_crypto_address (
        user_id,
        address_type,
        wallet_address
    ),

    INDEX idx_crypto_address_wallet (
        wallet_group_id
    ),

    INDEX idx_crypto_address_user (
        user_id
    )
);
