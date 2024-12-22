<?php
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/product/product_controller.php';

$productController = new ProductController();
$products = $productController->getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/../components/navbar.php'; ?>
        <main id="main-content">
            <div>
                <h2>All Products</h2>
                <div class="cards">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="card">
                                <div class="image-wrapper">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                                    <?php endif; ?>
                                </div>
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p>Seller: <?php echo htmlspecialchars($product['username']); ?></p>
                                <p>Description: <?php echo htmlspecialchars($product['description']); ?></p>
                                <p>Price: Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                <button class="add-to-cart" data-image="<?php echo $product['image']; ?>" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>">Add to Cart</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No products available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script src="../../assets/script.js"></script>
</body>
</html>
