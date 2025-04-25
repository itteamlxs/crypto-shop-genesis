
<?php require_once APP_ROOT . '/views/layout/header.php'; ?>

<div class="container">
    <h1 class="text-center mb-4">Our Products</h1>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card h-100">
                    <?php if ($product['image_url']): ?>
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                        <p class="card-text">
                            <strong>Price: $<?= number_format($product['price'], 2) ?></strong>
                        </p>
                        <a href="/product/<?= $product['id'] ?>" 
                           class="btn btn-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layout/footer.php'; ?>
