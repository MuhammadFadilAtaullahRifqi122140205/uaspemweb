<?php
require_once __DIR__ . '/../../app/controller/auth/auth_controller.php';
require_once __DIR__ . '/../../app/middleware/role_middleware.php';

adminMiddleware();

$authController = new AuthController();
$csrfToken = $authController->generateCsrfToken();

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
    <button class="btn btn-link toggle-dark-mode">
        <i id="dark-mode-icon" class="fas fa-moon"></i>
    </button>
    <a href="<?php echo getenv('APP_URL') . '/admin/dashboard/'; ?>"
       class="<?php echo isActive($currentUrl, '/admin/dashboard/'); ?>">Users</a>
    <a href="<?php echo getenv('APP_URL') . '/admin/product/'; ?>"
       class="<?php echo isActive($currentUrl, '/admin/product/'); ?>">Products</a>
    <form method="POST" action="<?php echo getenv('APP_URL') . '/auth.php?action=logout'; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <button type="submit" class="btn btn-link">Logout</button>
    </form>
</div>

<div class="navbar">
    <div class="profile">
        <img src="<?php echo htmlspecialchars($_SESSION["user"]["image"] ?? 'default-profile.png'); ?>" alt="Profile Picture">
        <h2 class="username"><?php echo htmlspecialchars($_SESSION["user"]['username'] ?? 'Guest'); ?></h2>
    </div>
    <button class="btn btn-link toggle-dark-mode">
        <i id="dark-mode-icon" class="fas fa-moon"></i>
    </button>
    <a href="<?php echo getenv('APP_URL') . '/admin/dashboard/'; ?>"
       class="<?php echo isActive($currentUrl, '/admin/dashboard/'); ?>">Users</a>
    <a href="<?php echo getenv('APP_URL') . '/admin/product/'; ?>"
       class="<?php echo isActive($currentUrl, '/admin/product/'); ?>">Products</a>
    <form method="POST" action="<?php echo getenv('APP_URL') . '/auth.php?action=logout'; ?>" style="display: inline;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <button type="submit" class="btn btn-link">Logout</button>
    </form>

    <script>
         $(document).ready(function() {
            // Cek preferensi dark mode dari localStorage saat halaman dimuat
            if (localStorage.getItem('darkMode') === 'enabled') {
                $('body').addClass('dark-mode');
                $('#dark-mode-icon').removeClass('fa-moon').addClass('fa-sun');
            }

            // Toggle dark mode dan simpan preferensi ke localStorage
            $('.toggle-dark-mode').click(function() {
                $('body').toggleClass('dark-mode');
                if ($('body').hasClass('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                    $('#dark-mode-icon').removeClass('fa-moon').addClass('fa-sun');
                } else {
                    localStorage.removeItem('darkMode');
                    $('#dark-mode-icon').removeClass('fa-sun').addClass('fa-moon');
                }
            });
        });
    </script>
</div>
