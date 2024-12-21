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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/../components/navbar.php'; ?>
        <main>
            <div>
                <h2>All Products</h2>
                <div class="cards">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="card">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p>Seller: <?php echo htmlspecialchars($product['user_id']); ?></p>
                                <p>Description: <?php echo htmlspecialchars($product['description']); ?></p>
                                <button class="add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>">Add to Cart</button>
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
    <script>
        $(document).ready(function() {
            $('.add-to-cart').on('click', function() {
                const productId = $(this).data('id');
                const productName = $(this).data('name');
                const productPrice = $(this).data('price');
                const product = { id: productId, name: productName, price: productPrice };

                let cart = [];
                const cartCookie = getCookie('cart');
                if (cartCookie) {
                    cart = JSON.parse(cartCookie);
                }

                cart.push(product);
                setCookie('cart', JSON.stringify(cart), 7);
                alert('Product added to cart');
            });

            function setCookie(name, value, days) {
                const d = new Date();
                d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
                const expires = "expires=" + d.toUTCString();
                document.cookie = name + "=" + value + ";" + expires + ";path=/";
            }

            function getCookie(name) {
                const nameEQ = name + "=";
                const ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }
        });
    </script>
</body>
</html>
