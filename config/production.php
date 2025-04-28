
<?php
/**
 * Production-specific configuration
 * This file contains settings that are only applied in production environment
 */

// Error reporting - hide errors in production
ini_set('display_errors', 0);
error_reporting(0);

// Set more secure session parameters
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// Set secure headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: same-origin');

// Enable Content Security Policy in production
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net; img-src 'self' https://api.qrserver.com data:;");

// Set PHP memory limit
ini_set('memory_limit', '256M');

// Set maximum execution time
ini_set('max_execution_time', 60);

// Log errors to a specific production log file
ini_set('error_log', APP_ROOT . '/logs/production-error.log');

echo "<!-- Production mode enabled -->";
