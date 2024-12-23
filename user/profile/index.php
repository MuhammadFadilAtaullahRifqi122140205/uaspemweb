<?php
// Memuat middleware untuk otentikasi
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
// Memuat controller untuk pengguna
require_once __DIR__ . '/../../app/controller/user/user_controller.php';

// Membuat instance dari UserController
$userController = new UserController();
// Mendapatkan data pengguna dari sesi
$user = $_SESSION['user'];

// Inisialisasi pesan toast
$toastMessage = '';
$toastType = '';

// Memeriksa apakah permintaan adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mendapatkan data dari form
    $username = $_POST['username'];
    $gender = $_POST['gender'];
    $city = $_POST['city'];

    // Memanggil fungsi untuk memperbarui profil pengguna
    $result = $userController->updateUserProfile($_SESSION['user']['id'], $username, $gender, $city, $imagePath);
    if ($result["status"] === 200) {
        // Jika berhasil, set pesan toast dan arahkan ke halaman profil
        $toastMessage = "Profile updated successfully.";
        $toastType = 'success';
        header("Location: /user/profile");
    } else {
        // Jika gagal, set pesan toast dengan pesan kesalahan
        $toastMessage = $result["message"];
        $toastType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/../components/navbar.php'; ?>
        <main id="main-content">
            <h2>Edit Profile</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="input-wrapper">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="input-wrapper">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="male" <?php echo $user['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $user['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="input-wrapper">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                </div>
                <div class="input-wrapper">
                    <label for="image">Profile Image:</label>
                    <input type="file" id="image" name="image">
                </div>
                <button type="submit">Update Profile</button>
            </form>
        </main>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Menampilkan pesan toast jika ada
            var toastMessage = <?php echo json_encode($toastMessage); ?>;
            var toastType = <?php echo json_encode($toastType); ?>;
            if (toastMessage) {
                toastr[toastType](toastMessage);
            }
        });
    </script>
    <script src="../../assets/script.js"></script>
</body>
</html>
