
<?php require APP_ROOT . '/views/layout/header.php'; ?>

<h1 class="mb-4">Your Shopping Cart</h1>

<?php if (empty($cartItems)): ?>
    <div class="alert alert-info">
        Your cart is empty. <a href="/">Continue shopping</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if (!empty($item['product']['image_url'])): ?>
                            <img src="/<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php endif; ?>
                            <div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($item['product']['name']); ?></h5>
                            </div>
                        </div>
                    </td>
                    <td><?php echo '$' . number_format($item['product']['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td class="text-end"><?php echo '$' . number_format($item['subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Total:</td>
                    <td class="text-end fw-bold"><?php echo '$' . number_format($total, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="d-flex justify-content-between mt-4">
        <a href="/" class="btn btn-outline-primary">Continue Shopping</a>
        <a href="/checkout" class="btn btn-success">Proceed to Checkout</a>
    </div>
<?php endif; ?>

<?php require APP_ROOT . '/views/layout/footer.php'; ?>
