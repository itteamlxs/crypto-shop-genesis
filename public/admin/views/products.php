<?php
if ($productManager->getProductAction()): ?>
    <!-- Product Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><?= $productManager->getProductAction() === 'create' ? 'Create New Product' : 'Edit Product' ?></h5>
        </div>
        <div class="card-body">
            <form method="post" action="dashboard.php?tab=products">
                <?= Csrf::tokenField() ?>
                
                <?php if ($productManager->getProductAction() === 'edit'): ?>
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($productManager->getCurrentProduct()['id']) ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= $productManager->getCurrentProduct() ? htmlspecialchars($productManager->getCurrentProduct()['name']) : '' ?>" 
                           required maxlength="255">
                    <div class="form-text">1-255 characters</div>
                </div>
                
                <div class="mb-3">
                    <label for="price" class="form-label">Price (USD)</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01" max="1000000" 
                           value="<?= $productManager->getCurrentProduct() ? htmlspecialchars($productManager->getCurrentProduct()['price']) : '' ?>" 
                           required>
                    <div class="form-text">Between $0.01 and $1,000,000</div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required maxlength="1000">
                        <?= $productManager->getCurrentProduct() ? htmlspecialchars($productManager->getCurrentProduct()['description']) : '' ?>
                    </textarea>
                    <div class="form-text">1-1000 characters</div>
                </div>
                
                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" min="0" max="10000" 
                           value="<?= $productManager->getCurrentProduct() ? htmlspecialchars($productManager->getCurrentProduct()['stock']) : '0' ?>" 
                           required>
                    <div class="form-text">Between 0 and 10,000</div>
                </div>
                
                <div class="mb-3">
                    <label for="image_url" class="form-label">Image URL (optional)</label>
                    <input type="text" class="form-control" id="image_url" name="image_url" 
                           value="<?= ($productManager->getCurrentProduct() && isset($productManager->getCurrentProduct()['image_url'])) ? htmlspecialchars($productManager->getCurrentProduct()['image_url']) : '' ?>" 
                           maxlength="255">
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
                <?php if (count($productManager->getAllProducts()) > 0): ?>
                    <?php foreach ($productManager->getAllProducts() as $product): ?>
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
