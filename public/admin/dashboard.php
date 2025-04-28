<?php
/**
 * Admin dashboard page
 */

// Load bootstrap configuration
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';
require_once __DIR__ . '/components/ProductManager.php';
require_once __DIR__ . '/components/OrderManager.php';

use App\Models\Admin;
use App\Csrf;

// Set security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!Admin::isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Check for session timeout (30 minutes)
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity'] > 1800)) {
    Admin::logout();
    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Process logout
if (isset($_GET['logout'])) {
    Admin::logout();
    header("Location: login.php");
    exit;
}

// Initialize managers
$productManager = new ProductManager();
$orderManager = new OrderManager();

// Handle product actions
$productManager->handleProductActions();

// Get current tab
$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'products';

// Generate CSRF token
$csrf_token = Csrf::getToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Crypto Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Crypto Shop Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentTab === 'products' ? 'active' : '' ?>" href="?tab=products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentTab === 'orders' ? 'active' : '' ?>" href="?tab=orders">Orders</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/" target="_blank">View Store</a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link text-light">Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="?logout=1">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <?php if ($productManager->getMessage()): ?>
            <div class="alert alert-<?= $productManager->getMessageType() ?>" role="alert">
                <?= $productManager->getMessage() ?>
            </div>
        <?php endif; ?>

        <!-- Dashboard Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text display-6"><?= count($productManager->getAllProducts()) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Paid Orders</h5>
                        <p class="card-text display-6"><?= $orderManager->getOrderStats()['paid'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Pending Orders</h5>
                        <p class="card-text display-6"><?= $orderManager->getOrderStats()['pending'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <p class="card-text display-6">$<?= number_format($orderManager->getTotalRevenue(), 2) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($currentTab === 'products'): ?>
            <?php require_once __DIR__ . '/views/products.php'; ?>
        <?php elseif ($currentTab === 'orders'): ?>
            <?php require_once __DIR__ . '/views/orders.php'; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
