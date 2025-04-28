<?php
/**
 * Admin dashboard page
 */

// Load bootstrap configuration
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';

use App\Models\Admin;
use App\Models\Product;
use App\Models\OrderAdmin;
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

// Handle product actions
$message = '';
$messageType = '';
$productAction = '';
$currentProduct = null;
$validationErrors = [];

// Handle product deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product']) && isset($_POST['product_id'])) {
    // Verify CSRF token
    if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
        $message = "Invalid request. Please try again.";
        $messageType = "danger";
    } else {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        
        // Validate product ID exists
        if ($productId) {
            // Check if product exists
            $checkProduct = Product::getByIdAdmin($productId);
            if ($checkProduct) {
                if (Product::deleteProduct($productId)) {
                    $message = "Product deleted successfully.";
                    $messageType = "success";
                } else {
                    $message = "Failed to delete product.";
                    $messageType = "danger";
                }
            } else {
                $message = "Invalid product ID.";
                $messageType = "danger";
            }
        } else {
            $message = "Invalid product ID.";
            $messageType = "danger";
        }
    }
}

// Handle product creation/update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_product'])) {
    // Verify CSRF token
    if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
        $message = "Invalid request. Please try again.";
        $messageType = "danger";
    } else {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
        $imageUrl = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL);
        
        // Validate inputs
        if (empty($name) || strlen($name) > 255) {
            $validationErrors[] = "Product name must be 1-255 characters.";
        }
        
        if ($price === false || $price < 0.01 || $price > 1000000) {
            $validationErrors[] = "Price must be between $0.01 and $1,000,000.";
        }
        
        if (empty($description) || strlen($description) > 1000) {
            $validationErrors[] = "Description must be 1-1000 characters.";
        }
        
        if ($stock === false || $stock < 0 || $stock > 10000) {
            $validationErrors[] = "Stock must be between 0 and 10,000.";
        }
        
        if (!empty($imageUrl) && strlen($imageUrl) > 255) {
            $validationErrors[] = "Image URL must be 1-255 characters.";
        }
        
        if (empty($validationErrors)) {
            if ($productId) {
                // Verify product exists before updating
                $checkProduct = Product::getByIdAdmin($productId);
                if (!$checkProduct) {
                    $message = "Invalid product ID.";
                    $messageType = "danger";
                } else {
                    // Update existing product
                    if (Product::updateProduct($productId, $name, $price, $description, $stock, $imageUrl)) {
                        $message = "Product updated successfully.";
                        $messageType = "success";
                    } else {
                        $message = "Failed to update product.";
                        $messageType = "danger";
                    }
                }
            } else {
                // Create new product
                if (Product::createProduct($name, $price, $description, $stock, $imageUrl)) {
                    $message = "Product created successfully.";
                    $messageType = "success";
                } else {
                    $message = "Failed to create product.";
                    $messageType = "danger";
                }
            }
        } else {
            $message = implode("<br>", $validationErrors);
            $messageType = "danger";
            
            // Keep form action for re-submission
            if ($productId) {
                $productAction = 'edit';
                $currentProduct = [
                    'id' => $productId,
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'stock' => $stock,
                    'image_url' => $imageUrl
                ];
            } else {
                $productAction = 'create';
                $currentProduct = [
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'stock' => $stock,
                    'image_url' => $imageUrl
                ];
            }
        }
    }
}

// Check for edit action
if (isset($_GET['edit']) && !isset($_POST['save_product'])) {
    $productId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($productId) {
        $currentProduct = Product::getByIdAdmin($productId);
        if ($currentProduct) {
            $productAction = 'edit';
        } else {
            $message = "Product not found.";
            $messageType = "danger";
        }
    }
}

// Check for create action
if (isset($_GET['action']) && $_GET['action'] === 'create' && !isset($_POST['save_product'])) {
    $productAction = 'create';
}

// Get products and orders for display
$products = Product::getAll();
$paidOrders = OrderAdmin::getPaidOrders();
$orderStats = OrderAdmin::getOrdersCountByStatus();
$totalRevenue = OrderAdmin::getTotalRevenue();

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
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>" role="alert">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Dashboard Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text display-6"><?= count($products) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Paid Orders</h5>
                        <p class="card-text display-6"><?= $orderStats['paid'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Pending Orders</h5>
                        <p class="card-text display-6"><?= $orderStats['pending'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <p class="card-text display-6">$<?= number_format($totalRevenue, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($currentTab === 'products'): ?>
            <!-- Products Tab -->
            <?php if ($productAction): ?>
                <!-- Product Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><?= $productAction === 'create' ? 'Create New Product' : 'Edit Product' ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="dashboard.php?tab=products">
                            <?= Csrf::tokenField() ?>
                            
                            <?php if ($productAction === 'edit'): ?>
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($currentProduct['id']) ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= $productAction === 'edit' || isset($currentProduct['name']) ? htmlspecialchars($currentProduct['name']) : '' ?>" required maxlength="255">
                                <div class="form-text">1-255 characters</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (USD)</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01" max="1000000" value="<?= $productAction === 'edit' || isset($currentProduct['price']) ? htmlspecialchars($currentProduct['price']) : '' ?>" required>
                                <div class="form-text">Between $0.01 and $1,000,000</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required maxlength="1000"><?= $productAction === 'edit' || isset($currentProduct['description']) ? htmlspecialchars($currentProduct['description']) : '' ?></textarea>
                                <div class="form-text">1-1000 characters</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" max="10000" value="<?= $productAction === 'edit' || isset($currentProduct['stock']) ? htmlspecialchars($currentProduct['stock']) : '0' ?>" required>
                                <div class="form-text">Between 0 and 10,000</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image_url" class="form-label">Image URL (optional)</label>
                                <input type="text" class="form-control" id="image_url" name="image_url" value="<?= ($productAction === 'edit' || isset($currentProduct['image_url'])) && isset($currentProduct['image_url']) ? htmlspecialchars($currentProduct['image_url']) : '' ?>" maxlength="255">
                                <div class="form-text">URL to product image (leave empty for default)</div>
                            </div>
                            
                            <div class="d-flex">
                                <button type="submit" name="save_product" class="btn btn-primary me-2">Save Product</button>
                                <a href="?tab=products" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Products List -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2>Products</h2>
                    <a href="?tab=products&action=create" class="btn btn-success">Add New Product</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['id']) ?></td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td>$<?= number_format($product['price'], 2) ?></td>
                                        <td><?= htmlspecialchars($product['stock']) ?></td>
                                        <td>
                                            <a href="?tab=products&edit=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <form method="post" action="dashboard.php?tab=products" class="d-inline">
                                                <?= Csrf::tokenField() ?>
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" name="delete_product" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No products found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php elseif ($currentTab === 'orders'): ?>
            <!-- Orders Tab -->
            <h2>Paid Orders</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Crypto Address</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($paidOrders) > 0): ?>
                            <?php foreach ($paidOrders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['id']) ?></td>
                                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                                    <td>$<?= number_format($order['amount'], 2) ?></td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="<?= htmlspecialchars($order['crypto_address']) ?>">
                                            <?= htmlspecialchars($order['crypto_address']) ?>
                                        </span>
                                    </td>
                                    <td><?= $order['email'] ? htmlspecialchars($order['email']) : '<em>No email</em>' ?></td>
                                    <td><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-success"><?= htmlspecialchars(ucfirst($order['status'])) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No paid orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
