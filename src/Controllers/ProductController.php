
<?php
namespace App\Controllers;

use App\Models\Product;

/**
 * Controller for product-related actions
 */
class ProductController {
    /**
     * Display product listing page
     */
    public function index() {
        $products = Product::findAll();
        require APP_ROOT . '/views/products/index.php';
    }
    
    /**
     * Display single product page
     * 
     * @param int $id Product ID
     */
    public function show($id) {
        $product = Product::findById($id);
        
        if (!$product) {
            header("Location: /");
            exit;
        }
        
        require APP_ROOT . '/views/products/show.php';
    }
}
