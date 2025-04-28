
<?php
$paidOrders = $orderManager->getPaidOrders();
?>
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
