
<?php
/**
 * Admin login page
 */

// Set secure session parameters
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 1800);
ini_set('session.cookie_samesite', 'Strict');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

// Start session with secure parameters
session_start();

// Load bootstrap configuration
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';

use App\Models\Admin;
use App\Csrf;

// Set security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Process logout
if (isset($_GET['logout'])) {
    Admin::logout();
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Check if admin is already logged in
if (Admin::isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Process login form
$error = null;
$blocked = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
        $error = "Invalid request. Please try again.";
    } else {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';
        
        // Check for rate limiting
        if (Admin::isIpBlocked($_SERVER['REMOTE_ADDR'])) {
            $error = "Too many failed login attempts. Please try again later.";
            $blocked = true;
        } else {
            // Attempt login
            if (Admin::login($username, $password)) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Set last activity timestamp
                $_SESSION['last_activity'] = time();
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                // Record failed login attempt
                Admin::recordLoginAttempt($_SERVER['REMOTE_ADDR']);
                $error = "Invalid username or password.";
            }
        }
    }
}

// Generate CSRF token
$csrf_token = Csrf::generateToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Crypto Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Admin Login</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$blocked): ?>
                            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                                <?= Csrf::tokenField() ?>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required autofocus maxlength="50">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required maxlength="255">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <div class="mt-3 text-center">
                            <a href="/" class="text-decoration-none">← Back to Store</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
