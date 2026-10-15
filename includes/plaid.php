<?php

declare(strict_types=1);

function plaid_config(): array
{
    $path = APP_ROOT . '/plaid.local.php';

    if (!is_file($path)) {
        throw new RuntimeException(
            'Plaid local configuration is missing.'
        );
    }

    $config = require $path;

    if (
        empty($config['client_id']) ||
        empty($config['secret'])
    ) {
        throw new RuntimeException(
            'Plaid credentials are incomplete.'
        );
    }

    return $config;
}


function plaid_base_url(): string
{
    $config = plaid_config();

    return match (
        $config['environment'] ?? 'sandbox'
    ) {
        'sandbox' =>
            'https://sandbox.plaid.com',

        'production' =>
            'https://production.plaid.com',

        default =>
            'https://sandbox.plaid.com',
    };
}


function plaid_request(
    string $endpoint,
    array $payload
): array {
    $config = plaid_config();

    $payload['client_id'] =
        $config['client_id'];

    $payload['secret'] =
        $config['secret'];

    $ch = curl_init(
        plaid_base_url() . $endpoint
    );

    curl_setopt_array(
        $ch,
        [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Plaid-Version: 2020-09-14',
            ],

            CURLOPT_POSTFIELDS =>
                json_encode(
                    $payload,
                    JSON_THROW_ON_ERROR
                ),

            CURLOPT_TIMEOUT => 30,
        ]
    );

    $response = curl_exec($ch);

    if ($response === false) {
        throw new RuntimeException(
            'Plaid request failed: '
            . curl_error($ch)
        );
    }

    $status = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    $data = json_decode(
        $response,
        true
    );

    if (!is_array($data)) {
        throw new RuntimeException(
            'Plaid returned an invalid response.'
        );
    }

    if ($status >= 400) {
        throw new RuntimeException(
            (string) (
                $data['error_message']
                ?? 'Plaid request failed.'
            )
        );
    }

    return $data;
}


function plaid_token_key(): string
{
    $config = plaid_config();

    $encodedKey = (string) (
        $config['token_key']
        ?? ''
    );

    if ($encodedKey === '') {
        throw new RuntimeException(
            'Plaid token encryption key is missing.'
        );
    }

    $key = base64_decode(
        $encodedKey,
        true
    );

    if (
        $key === false ||
        strlen($key) !== 32
    ) {
        throw new RuntimeException(
            'Plaid token encryption key is invalid.'
        );
    }

    return $key;
}


function plaid_encrypt_token(
    string $token
): string {
    $cipher = 'aes-256-gcm';

    $ivLength =
        openssl_cipher_iv_length(
            $cipher
        );

    if ($ivLength === false) {
        throw new RuntimeException(
            'AES-256-GCM is unavailable.'
        );
    }

    $iv = random_bytes(
        $ivLength
    );

    $tag = '';

    $ciphertext =
        openssl_encrypt(
            $token,
            $cipher,
            plaid_token_key(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

    if ($ciphertext === false) {
        throw new RuntimeException(
            'Plaid token encryption failed.'
        );
    }

    return 'v1:'
        . base64_encode(
            $iv
            . $tag
            . $ciphertext
        );
}


function plaid_decrypt_token(
    string $encrypted
): string {
    if (
        !str_starts_with(
            $encrypted,
            'v1:'
        )
    ) {
        throw new RuntimeException(
            'Unsupported Plaid token format.'
        );
    }

    $decoded = base64_decode(
        substr(
            $encrypted,
            3
        ),
        true
    );

    if ($decoded === false) {
        throw new RuntimeException(
            'Encrypted Plaid token is invalid.'
        );
    }

    $cipher = 'aes-256-gcm';

    $ivLength =
        openssl_cipher_iv_length(
            $cipher
        );

    if ($ivLength === false) {
        throw new RuntimeException(
            'AES-256-GCM is unavailable.'
        );
    }

    $tagLength = 16;

    if (
        strlen($decoded)
        <= $ivLength + $tagLength
    ) {
        throw new RuntimeException(
            'Encrypted Plaid token is malformed.'
        );
    }

    $iv = substr(
        $decoded,
        0,
        $ivLength
    );

    $tag = substr(
        $decoded,
        $ivLength,
        $tagLength
    );

    $ciphertext = substr(
        $decoded,
        $ivLength + $tagLength
    );

    $token =
        openssl_decrypt(
            $ciphertext,
            $cipher,
            plaid_token_key(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

    if ($token === false) {
        throw new RuntimeException(
            'Plaid token could not be decrypted.'
        );
    }

    return $token;
}


function plaid_account_type(
    string $type,
    ?string $subtype
): string {
    if ($type === 'credit') {
        return 'credit';
    }

    if ($type === 'investment') {
        return 'investment';
    }

    if ($type === 'loan') {
        return 'loan';
    }

    if ($type === 'depository') {
        return match ($subtype) {
            'checking' => 'checking',
            'savings' => 'savings',
            default => 'checking',
        };
    }

    return 'other';
}
