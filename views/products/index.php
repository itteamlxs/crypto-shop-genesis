
<?php require APP_ROOT . '/views/layout/header.php'; ?>

<h1 class="mb-4">Product Catalog</h1>

<div class="row">
    <?php foreach ($products as $product): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <?php if (!empty($product['image_url'])): ?>
            <img src="/<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php else: ?>
            <div class="card-img-top bg-light text-center py-5">No Image</div>
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                <p class="card-text text-truncate"><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5"><?php echo '$' . number_format($product['price'], 2); ?></span>
                    <a href="/product/<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require APP_ROOT . '/views/layout/footer.php'; ?>
