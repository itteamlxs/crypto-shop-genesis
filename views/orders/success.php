
<?php require APP_ROOT . '/views/layout/header.php'; ?>

<div class="text-center my-5">
    <div class="mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
    </div>
    
    <h1 class="mb-4">Payment Successful!</h1>
    <p class="lead mb-4">Your order has been confirmed and will be processed shortly.</p>
    <p>An email confirmation has been sent with your order details.</p>
    
    <div class="mt-5">
        <a href="/" class="btn btn-primary">Continue Shopping</a>
    </div>
</div>

<?php require APP_ROOT . '/views/layout/footer.php'; ?>
