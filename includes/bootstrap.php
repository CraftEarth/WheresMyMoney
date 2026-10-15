<?php

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('APP_NAME', "Where's My Money?");
define('APP_VERSION', '0.2.0');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once APP_ROOT . '/includes/functions.php';
require_once APP_ROOT . '/includes/database.php';
require_once APP_ROOT . '/includes/auth.php';
require_once APP_ROOT . '/includes/ledger.php';

require_once APP_ROOT . '/includes/normalization.php';

require_once APP_ROOT . '/includes/plaid.php';
