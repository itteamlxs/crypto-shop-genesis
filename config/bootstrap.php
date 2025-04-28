
<?php
/**
 * Bootstrap file for application configuration
 * Loads environment variables and sets up basic configuration
 */

// Define the application root directory
define('APP_ROOT', dirname(__DIR__));

// Require the autoloader
require_once APP_ROOT . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->safeLoad();

// Include the main configuration
require_once APP_ROOT . '/config/config.php';

// Load environment-specific configuration
if (isset($_ENV['ENVIRONMENT']) && $_ENV['ENVIRONMENT'] === 'production') {
    require_once APP_ROOT . '/config/production.php';
}
