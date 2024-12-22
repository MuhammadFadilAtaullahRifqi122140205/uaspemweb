<?php
require_once __DIR__ . '/../../app/controller/auth/auth_controller.php';
require_once __DIR__ . '/../../app/middleware/role_middleware.php';

userMiddleware();

$authController = new AuthController();
$csrfToken = $authController->generateCsrfToken();

// Helper function to generate active class for links
function isActive($currentUrl, $linkPath) {
    return parse_url($currentUrl, PHP_URL_PATH) === $linkPath ? 'nav-link-active' : '';
}

$currentUrl = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar">
    <div class="profile">
        <img src="<?php echo htmlspecialchars($_SESSION["user"]["image"] ?? 'default-profile.png'); ?>" alt="Profile Picture">
        <h2><?php echo htmlspecialchars($_SESSION["user"]['username'] ?? 'Guest'); ?></h2>
    </div>
    <a href="<?php echo getenv('APP_URL') . '/user/dashboard'; ?>"
       class="<?php echo isActive($currentUrl, '/user/dashboard'); ?>">Home</a>
    <a href="<?php echo getenv('APP_URL') . '/user/profile'; ?>"
       class="<?php echo isActive($currentUrl, '/user/profile'); ?>">Profile</a>
    <a href="<?php echo getenv('APP_URL') . '/user/product'; ?>"
       class="<?php echo isActive($currentUrl, '/user/product'); ?>">Add Product</a>
    <div class="cart-icon">
        <i class="fas fa-shopping-cart" style="font-size: 30px;"></i>
    </div>
    <form method="POST" action="<?php echo getenv('APP_URL') . '/auth.php?action=logout'; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <button type="submit" class="btn btn-link">Logout</button>
    </form>
</div>

<div class="navbar">
    <div class="profile">
        <img src="<?php echo htmlspecialchars($_SESSION["user"]["image"] ?? 'default-profile.png'); ?>" alt="Profile Picture">
        <h2><?php echo htmlspecialchars($_SESSION["user"]['username'] ?? 'Guest'); ?></h2>
    </div>
    <a href="<?php echo getenv('APP_URL') . '/user/dashboard'; ?>"
       class="<?php echo isActive($currentUrl, '/user/dashboard'); ?>">Home</a>
    <a href="<?php echo getenv('APP_URL') . '/user/profile'; ?>"
       class="<?php echo isActive($currentUrl, '/user/profile'); ?>">Profile</a>
    <a href="<?php echo getenv('APP_URL') . '/user/product'; ?>"
       class="<?php echo isActive($currentUrl, '/user/product'); ?>">Add Product</a>
    <div class="cart-icon">
        <i class="fas fa-shopping-cart" style="font-size: 30px;"></i>
    </div>
    <form method="POST" action="<?php echo getenv('APP_URL') . '/auth.php?action=logout'; ?>" style="display: inline;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <button type="submit" class="btn btn-link">Logout</button>
    </form>
</div>
