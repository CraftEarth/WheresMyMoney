<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

header(
    'Content-Type: application/json'
);

try {
    $response = plaid_request(
        '/link/token/create',
        [
            'user' => [
                'client_user_id' =>
                    (string) current_user_id(),
            ],

            'client_name' =>
                "Where's My Money?",

            'products' => [
                'transactions',
            ],

            'country_codes' => [
                'US',
            ],

            'language' => 'en',
        ]
    );

    echo json_encode(
        [
            'success' => true,
            'link_token' =>
                $response['link_token'],
        ],
        JSON_THROW_ON_ERROR
    );
} catch (Throwable $exception) {
    http_response_code(500);

    echo json_encode(
        [
            'success' => false,
            'error' =>
                $exception->getMessage(),
        ],
        JSON_THROW_ON_ERROR
    );
}
