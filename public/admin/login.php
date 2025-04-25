
<?php
/**
 * Admin login page
 */

// Load bootstrap configuration
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';

use App\Models\Admin;

// Set security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Check if admin is already logged in
if (Admin::isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Process login form
$error = null;
$blocked = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';
    
    // Check for rate limiting
    if (Admin::isIpBlocked($_SERVER['REMOTE_ADDR'])) {
        $error = "Too many failed login attempts. Please try again later.";
        $blocked = true;
    } else {
        // Attempt login
        if (Admin::login($username, $password)) {
            header("Location: dashboard.php");
            exit;
        } else {
            // Record failed login attempt
            Admin::recordLoginAttempt($_SERVER['REMOTE_ADDR']);
            $error = "Invalid username or password.";
        }
    }
}
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
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
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
