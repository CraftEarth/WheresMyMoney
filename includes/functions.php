<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}

function url(string $path = ''): string
{
    return '/WheresMyMoney/' . ltrim($path, '/');
}

function money(float $amount): string
{
    return '$' . number_format($amount, 2);
}
