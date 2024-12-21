<?php
require_once __DIR__ . '/../../app/controller/user/user_controller.php';
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../");
    exit();
}

// Instantiate the UserController
$userController = new UserController();

// Fetch user data using the UserController
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
    <div class="container">
        <div class="sidebar">
            <h2>User Dashboard</h2>
            <a href="#">Home</a>
            <a href="#">Profile</a>
            <a href="#">Settings</a>
            <form method="POST" action="/auth.php?action=logout">
                <button type="submit" class="btn btn-link">Logout</button>
            </form>
        </div>

        <div class="navbar">
            <a href="#">Home</a>
            <a href="#">Profile</a>
            <a href="#">Settings</a>
            <form method="POST" action="/auth.php?action=logout" style="display: inline;">
                <button type="submit" class="btn btn-link">Logout</button>
            </form>
        </div>

        <main></main>
    </div>
</body>
</html>
