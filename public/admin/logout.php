
<?php
/**
 * Admin logout endpoint
 */

// Load bootstrap configuration
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';

use App\Models\Admin;

// Logout the admin
Admin::logout();

// Redirect to login page
header("Location: login.php");
exit;
