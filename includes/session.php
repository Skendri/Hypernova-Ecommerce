<?php

/**
 * Start the application session with cookie protections applied before headers are sent.
 */
function appSessionCookieOptions(): array
{
    $isHttps = isset($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== ''
        && strtolower((string) $_SERVER['HTTPS']) !== 'off';

    return [
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(appSessionCookieOptions());
    session_start();
}

/** Clear server-side session state and expire the browser's session cookie. */
function destroyAppSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $_SESSION = [];
    $cookieOptions = appSessionCookieOptions();
    unset($cookieOptions['lifetime']);
    $cookieOptions['expires'] = time() - 42000;
    setcookie(session_name(), '', $cookieOptions);
    session_destroy();
}
