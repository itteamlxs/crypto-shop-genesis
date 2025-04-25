
<?php require APP_ROOT . '/views/layout/header.php'; ?>

<nav aria-label="breadcrumb" class="my-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Products</a></li>
        <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-6 mb-4">
        <?php if (!empty($product['image_url'])): ?>
        <img src="/<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php else: ?>
        <div class="bg-light text-center py-5 rounded">No Image</div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-6">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="lead text-primary fw-bold"><?php echo '$' . number_format($product['price'], 2); ?></p>
        
        <div class="mb-4">
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
        
        <div class="mb-3">
            <span class="badge <?php echo $product['stock'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
            </span>
            <?php if ($product['stock'] > 0): ?>
            <span class="text-muted ms-2"><?php echo $product['stock']; ?> items available</span>
            <?php endif; ?>
        </div>
        
        <?php if ($product['stock'] > 0): ?>
        <form action="/cart/add" method="post" class="d-flex align-items-center">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <div class="input-group me-3" style="max-width: 120px;">
                <span class="input-group-text">Qty</span>
                <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Add to Cart</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php require APP_ROOT . '/views/layout/footer.php'; ?>
