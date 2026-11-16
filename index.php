<?php
declare(strict_types=1);

ob_start(); // Buffer all output so redirects work even after header.php has run

// Block direct access to sensitive directories
$_uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
if (preg_match('#^/(database|config|src|includes)(/|$)#i', $_uri)) {
    http_response_code(403);
    exit('Forbidden');
}

// Harden the session cookie before the session starts. Secure is automatic on
// HTTPS and can be forced with MERIDIAN_COOKIE_SECURE=1 behind a TLS proxy.
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? '') === '443');
$cookieSecure = filter_var(
    getenv('MERIDIAN_COOKIE_SECURE') ?: ($https ? '1' : '0'),
    FILTER_VALIDATE_BOOLEAN
);
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $cookieSecure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();

define('ROOT_PATH', __DIR__);
define('APP_NAME',  'Meridian Horizons');

require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/src/TravelManager.php';
require_once ROOT_PATH . '/includes/functions.php';

const VALID_PAGES = [
    'home', 'about', 'contact', 'search_results',
    'booking_details', 'basket', 'login', 'register', 'logout',
    'admin_dashboard', 'admin_manage_listings', 'admin_view_users', 'admin_booking_detail',
    'faq', 'terms', 'privacy', 'cookies', 'forgot_password',
    '404', 'access_denied',
];

const ADMIN_PAGES = ['admin_dashboard', 'admin_manage_listings', 'admin_view_users', 'admin_booking_detail'];
const AUTH_PAGES  = [];

$page = isset($_GET['page']) ? trim((string) $_GET['page']) : 'home';

if (!in_array($page, VALID_PAGES, true)) {
    $page = '404';
}

if (in_array($page, ADMIN_PAGES, true)) {
    requireAuth();
    requireRole('admin');
}

if (in_array($page, AUTH_PAGES, true)) {
    requireAuth();
}

$tm = null;
try {
    $db = getDbConnection();
    $tm = new TravelManager($db);
} catch (PDOException $e) {
    error_log('[Meridian] DB unavailable: ' . $e->getMessage());
}

require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/pages/' . $page . '.php';
require_once ROOT_PATH . '/includes/footer.php';
