<?php
declare(strict_types=1);

// ── Output helpers ────────────────────────────────────────────────────────

/** Escape output for safe HTML insertion. */
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Return one visible initial without requiring mbstring. */
function initial(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '?';
    }
    $first = function_exists('mb_substr')
        ? mb_substr($value, 0, 1, 'UTF-8')
        : substr($value, 0, 1);
    return function_exists('mb_strtoupper')
        ? mb_strtoupper($first, 'UTF-8')
        : strtoupper($first);
}

/** Format a price as a GBP string (e.g. £2,450). */
function formatPrice(float $price): string
{
    return '£' . number_format($price, 0, '.', ',');
}

/** Format a MySQL date string to a human-readable format. */
function formatDate(string $date): string
{
    $ts = strtotime($date);
    return $ts ? date('j F Y', $ts) : $date;
}

/** Convert a string to a URL-safe slug. */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ── CSRF protection ──────────────────────────────────────────────────

/** Return (and generate if needed) the session CSRF token. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Render a hidden CSRF input field. */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrfToken()) . '">';
}

/** Verify CSRF token from POST. Exits with 403 on failure. */
function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        exit('Forbidden: invalid request token.');
    }
}

// ── Authentication & authorisation ───────────────────────────────────

/** Returns true if a user is logged in. */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/** Returns the current user array from session, or null. */
function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id'    => (int)  $_SESSION['user_id'],
        'name'  => (string) ($_SESSION['user_name']  ?? ''),
        'email' => (string) ($_SESSION['user_email'] ?? ''),
        'role'  => (string) ($_SESSION['user_role']  ?? 'user'),
    ];
}

/** Redirect to login page if the user is not authenticated. */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        flash('info', 'Please sign in to continue.');
        redirect('login');
    }
}

/** Redirect to access-denied page if the user lacks the required role. */
function requireRole(string $role): void
{
    $user = currentUser();
    if ($user === null || $user['role'] !== $role) {
        redirect('access_denied');
    }
}

// ── Flash messages ────────────────────────────────────────────────────

/**
 * Store a one-time flash message.
 * Types: 'success' | 'error' | 'info' | 'warning'
 */
function flash(string $type, string $message): void
{
    $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Retrieve and clear the flash message.
 * Returns ['type' => ..., 'message' => ...] or null.
 */
function getFlash(): ?array
{
    if (!isset($_SESSION['_flash'])) {
        return null;
    }
    $flash = $_SESSION['_flash'];
    unset($_SESSION['_flash']);
    return $flash;
}

/** Render the flash banner HTML if one exists. */
function renderFlash(): void
{
    $f = getFlash();
    if ($f === null) {
        return;
    }
    $icon = match($f['type']) {
        'success' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
        'error'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        'warning' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        default   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
    };
    echo '<div class="flash flash--' . h($f['type']) . '" role="alert">';
    echo '<span class="flash__icon">' . $icon . '</span>';
    echo '<span class="flash__message">' . h($f['message']) . '</span>';
    echo '<button class="flash__close" onclick="this.parentElement.remove()" aria-label="Dismiss">&times;</button>';
    echo '</div>';
}

// ── Navigation helpers ────────────────────────────────────────────────

/** Redirect to a page (sends Location header and exits). */
function redirect(string $page, array $params = []): never
{
    $params['page'] = $page;
    $url = '/?' . http_build_query($params);
    // Ensure we only redirect to internal relative paths
    header('Location: ' . $url);
    exit;
}

/** Returns the current page slug. */
function currentPage(): string
{
    return isset($_GET['page']) ? trim((string) $_GET['page']) : 'home';
}

/** Returns " active" if $page matches the current page (for nav classes). */
function navActive(string $page): string
{
    return currentPage() === $page ? ' active' : '';
}

// ── Pagination helper ─────────────────────────────────────────────────

/** Returns pagination metadata for a query result set. */
function paginate(int $total, int $currentPage, int $perPage = 12): array
{
    $totalPages = (int) ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages ?: 1));
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $currentPage,
        'total_pages' => $totalPages,
        'offset'      => ($currentPage - 1) * $perPage,
        'has_prev'    => $currentPage > 1,
        'has_next'    => $currentPage < $totalPages,
    ];
}

// ── Form validation ───────────────────────────────────────────────────

/** Returns the POST value, or '' if absent. Applies basic trim. */
function post(string $key): string
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

/** Returns the GET value, or '' if absent. Applies basic trim. */
function get(string $key): string
{
    return isset($_GET[$key]) ? trim((string) $_GET[$key]) : '';
}
