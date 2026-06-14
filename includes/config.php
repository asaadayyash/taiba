<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'taiba_store');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Taiba Gold Store');
define('SITE_URL', 'http://localhost/taiba-store');
define('CURRENCY', 'SAR');
define('VAT_RATE', 0.15);
define('FREE_SHIPPING_THRESHOLD', 500);
define('SHIPPING_COST', 30);

session_name('taiba_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
