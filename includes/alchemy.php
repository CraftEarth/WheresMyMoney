<?php

declare(strict_types=1);


function alchemy_config(): array
{
    $path =
        APP_ROOT . '/alchemy.local.php';

    if (!is_file($path)) {
        throw new RuntimeException(
            'alchemy.local.php was not found.'
        );
    }

    $config = require $path;

    $apiKey = trim(
        (string) (
            $config['api_key']
            ?? ''
        )
    );

    if ($apiKey === '') {
        throw new RuntimeException(
            'Alchemy API key is not configured.'
        );
    }

    return [
        'api_key' => $apiKey,
    ];
}


function alchemy_rpc(
    string $url,
    string $method,
    array $params = []
): array {
    $payload = json_encode(
        [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => $method,
            'params' => $params,
        ],
        JSON_THROW_ON_ERROR
    );

    $ch = curl_init($url);

    curl_setopt_array(
        $ch,
        [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 30,
        ]
    );

    $body = curl_exec($ch);

    if ($body === false) {
        $message = curl_error($ch);

        curl_close($ch);

        throw new RuntimeException(
            'Alchemy request failed: '
            . $message
        );
    }

    $status =
        (int) curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

    curl_close($ch);

    $data = json_decode(
        $body,
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    if (
        $status < 200 ||
        $status >= 300
    ) {
        throw new RuntimeException(
            'Alchemy HTTP error '
            . $status
        );
    }

    if (isset($data['error'])) {
        throw new RuntimeException(
            'Alchemy error: '
            . (
                $data['error']['message']
                ?? 'Unknown RPC error'
            )
        );
    }

    return $data;
}


function alchemy_eth_balance(
    string $address
): float {
    $config = alchemy_config();

    $url =
        'https://eth-mainnet.g.alchemy.com/v2/'
        . rawurlencode(
            $config['api_key']
        );

    $response = alchemy_rpc(
        $url,
        'eth_getBalance',
        [
            $address,
            'latest',
        ]
    );

    $hex = (string) (
        $response['result']
        ?? '0x0'
    );

    /*
     * Ethereum balance comes back in wei.
     * hexdec() is sufficient for the tiny wallet
     * balances we are testing right now.
     */
    $wei = hexdec($hex);

    return $wei / 1e18;
}


function alchemy_sol_balance(
    string $address
): float {
    $config = alchemy_config();

    $url =
        'https://solana-mainnet.g.alchemy.com/v2/'
        . rawurlencode(
            $config['api_key']
        );

    $response = alchemy_rpc(
        $url,
        'getBalance',
        [
            $address,
            [
                'commitment' => 'confirmed',
            ],
        ]
    );

    $lamports = (float) (
        $response['result']['value']
        ?? 0
    );

    return $lamports / 1e9;
}
