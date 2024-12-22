<?php
require_once __DIR__ . '/../../app/controller/product/product_controller.php';

$productController = new ProductController();
$csrfToken = $productController->generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = $productController->handleRequest();
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $product = $productController->getProductById($_GET['id']);
    echo json_encode($product);
    exit();
}

$products = $productController->getMyProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>
    <div class="layout" style="position: relative;">
        <?php include __DIR__ . '/../components/navbar.php'; ?>
        <main id="main-content">
            <h2>Product Management</h2>
            <button id="add-product-button">Add Product</button>
            <div class="cards">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p>Price: <?php echo htmlspecialchars($product['price']); ?></p>
                            <p>Description: <?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="image-wrapper">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                                <?php endif; ?>
                            </div>
                            <div class="btn-wrapper">
                                <button class="edit-product" data-id="<?php echo $product['id']; ?>">Edit</button>
                                <button class="delete-product" data-id="<?php echo $product['id']; ?>">Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </main>
        <div id="product-modal" class="modal">
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <form id="product-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="action">
                    <input type="hidden" name="id">
                    <div class="input-wrapper">
                        <label for="name">Name:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="price">Price:</label>
                        <input type="text" name="price" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="description">Description:</label>
                        <input type="text" name="description" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="image">Image:</label>
                        <input type="file" name="image" id="image">
                        <img id="image-preview" src="#" alt="Image Preview" style="display: none;">
                    </div>
                    <button type="submit">Save</button>
                </form>
            </div>
        </div>

        <div id="delete-modal" class="modal">
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <form id="delete-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id">
                    <p>Are you sure you want to delete this product?</p>
                    <div class="btn-wrapper">
                        <button type="submit">Yes</button>
                        <button id="cancel-delete" class="btn-danger">No</button>
                    </div>
                </form>
            </div>
        </div>
</div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script src="../../assets/script.js"></script>
</body>
</html>
