<?php
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/user/user_controller.php';

$userController = new UserController();

$users = $userController->getAllUsers();
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
            <div>
                <h2>All Books</h2>
                <div class="cards">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <div class="card">
                            <h3>Book Title <?php echo $i; ?></h3>
                            <p>Author: Author <?php echo $i; ?></p>
                            <p>Description: This is a description for book <?php echo $i; ?>.</p>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
