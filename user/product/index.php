<?php
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/product/product_controller.php';

$productController = new ProductController();
$myProducts = $productController->getMyProducts();
$csrfToken = $productController->generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$productController->verifyCsrfToken($_POST['csrf_token'])) {
        echo "Invalid CSRF token.";
        exit();
    }

    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $productController->createProduct($name, $price, $description);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/../components/navbar.php'; ?>
        <main>
            <section>
                <h2>Add Product</h2>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <div class="input-wrapper">
                        <label for="name">Product Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <button type="submit">Add Product</button>
                </form>
            </section>
            <section>
                <h2>My Products</h2>
                <?php if (!empty($myProducts)): ?>
                    <ul>
                        <?php foreach ($myProducts as $product): ?>
                            <li>
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                                <p><?php echo htmlspecialchars($product['description']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>You have no products.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
