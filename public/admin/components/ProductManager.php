
<?php
require_once dirname(dirname(dirname(__DIR__))) . '/src/Models/Product.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/Csrf.php';

class ProductManager {
    private $message = '';
    private $messageType = '';
    private $productAction = '';
    private $currentProduct = null;
    private $validationErrors = [];

    public function handleProductActions() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['delete_product']) && isset($_POST['product_id'])) {
                $this->handleProductDeletion();
            } elseif (isset($_POST['save_product'])) {
                $this->handleProductSave();
            }
        }

        if (isset($_GET['edit']) && !isset($_POST['save_product'])) {
            $this->handleEditAction();
        }

        if (isset($_GET['action']) && $_GET['action'] === 'create' && !isset($_POST['save_product'])) {
            $this->productAction = 'create';
        }
    }

    private function handleProductDeletion() {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->message = "Invalid request. Please try again.";
            $this->messageType = "danger";
            return;
        }

        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        if ($productId) {
            $checkProduct = Product::getByIdAdmin($productId);
            if ($checkProduct) {
                if (Product::deleteProduct($productId)) {
                    $this->message = "Product deleted successfully.";
                    $this->messageType = "success";
                } else {
                    $this->message = "Failed to delete product.";
                    $this->messageType = "danger";
                }
            } else {
                $this->message = "Invalid product ID.";
                $this->messageType = "danger";
            }
        } else {
            $this->message = "Invalid product ID.";
            $this->messageType = "danger";
        }
    }

    private function handleProductSave() {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->message = "Invalid request. Please try again.";
            $this->messageType = "danger";
            return;
        }

        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
        $imageUrl = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL);

        $this->validateProductInputs($name, $price, $description, $stock, $imageUrl);

        if (empty($this->validationErrors)) {
            if ($productId) {
                $this->updateExistingProduct($productId, $name, $price, $description, $stock, $imageUrl);
            } else {
                $this->createNewProduct($name, $price, $description, $stock, $imageUrl);
            }
        } else {
            $this->handleValidationErrors($productId, $name, $price, $description, $stock, $imageUrl);
        }
    }

    private function validateProductInputs($name, $price, $description, $stock, $imageUrl) {
        if (empty($name) || strlen($name) > 255) {
            $this->validationErrors[] = "Product name must be 1-255 characters.";
        }
        
        if ($price === false || $price < 0.01 || $price > 1000000) {
            $this->validationErrors[] = "Price must be between $0.01 and $1,000,000.";
        }
        
        if (empty($description) || strlen($description) > 1000) {
            $this->validationErrors[] = "Description must be 1-1000 characters.";
        }
        
        if ($stock === false || $stock < 0 || $stock > 10000) {
            $this->validationErrors[] = "Stock must be between 0 and 10,000.";
        }
        
        if (!empty($imageUrl) && strlen($imageUrl) > 255) {
            $this->validationErrors[] = "Image URL must be 1-255 characters.";
        }
    }

    private function updateExistingProduct($productId, $name, $price, $description, $stock, $imageUrl) {
        $checkProduct = Product::getByIdAdmin($productId);
        if (!$checkProduct) {
            $this->message = "Invalid product ID.";
            $this->messageType = "danger";
            return;
        }

        if (Product::updateProduct($productId, $name, $price, $description, $stock, $imageUrl)) {
            $this->message = "Product updated successfully.";
            $this->messageType = "success";
        } else {
            $this->message = "Failed to update product.";
            $this->messageType = "danger";
        }
    }

    private function createNewProduct($name, $price, $description, $stock, $imageUrl) {
        if (Product::createProduct($name, $price, $description, $stock, $imageUrl)) {
            $this->message = "Product created successfully.";
            $this->messageType = "success";
        } else {
            $this->message = "Failed to create product.";
            $this->messageType = "danger";
        }
    }

    private function handleValidationErrors($productId, $name, $price, $description, $stock, $imageUrl) {
        $this->message = implode("<br>", $this->validationErrors);
        $this->messageType = "danger";
        
        if ($productId) {
            $this->productAction = 'edit';
            $this->currentProduct = [
                'id' => $productId,
                'name' => $name,
                'price' => $price,
                'description' => $description,
                'stock' => $stock,
                'image_url' => $imageUrl
            ];
        } else {
            $this->productAction = 'create';
            $this->currentProduct = [
                'name' => $name,
                'price' => $price,
                'description' => $description,
                'stock' => $stock,
                'image_url' => $imageUrl
            ];
        }
    }

    private function handleEditAction() {
        $productId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
        if ($productId) {
            $this->currentProduct = Product::getByIdAdmin($productId);
            if ($this->currentProduct) {
                $this->productAction = 'edit';
            } else {
                $this->message = "Product not found.";
                $this->messageType = "danger";
            }
        }
    }

    public function getMessage() {
        return $this->message;
    }

    public function getMessageType() {
        return $this->messageType;
    }

    public function getProductAction() {
        return $this->productAction;
    }

    public function getCurrentProduct() {
        return $this->currentProduct;
    }

    public function getAllProducts() {
        return Product::getAll();
    }
}
