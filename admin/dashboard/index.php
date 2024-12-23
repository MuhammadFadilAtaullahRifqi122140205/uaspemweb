<?php
// Memuat file user_controller.php yang berisi kelas UserController
require_once __DIR__ . '/../../app/controller/user/user_controller.php';

// Membuat instance dari UserController
$userController = new UserController();
// Menghasilkan token CSRF
$csrfToken = $userController->generateCsrfToken();
// Menangani permintaan
$userController->handleRequest();

// Mendapatkan semua pengguna
$users = $userController->getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>
    <div class="layout" style="position: relative;">
        <?php include __DIR__ . '/../components/navbar.php'; ?>
        <main id="main-content">
            <h2>User Management</h2>
            <button id="add-user-button">Add User</button>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Gender</th>
                        <th>City</th>
                        <th>IP Address</th>
                        <th>Browser</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['gender']); ?></td>
                                <td><?php echo htmlspecialchars($user['city']); ?></td>
                                <td><?php echo htmlspecialchars($user['ip_address']); ?></td>
                                <td><?php echo htmlspecialchars($user['browser']); ?></td>
                                <td>
                                    <button class="edit-user" data-id="<?php echo $user['id']; ?>">Edit</button>
                                    <button class="delete-user" data-id="<?php echo $user['id']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
        <div id="user-modal" class="modal">
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <form id="user-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="action">
                    <input type="hidden" name="id">
                    <div class="input-wrapper">
                        <label for="username">Username:</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="gender">Gender:</label>
                        <select name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="input-wrapper">
                        <label for="city">City:</label>
                        <input type="text" name="city" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="ip">IP Address:</label>
                        <input type="text" name="ip" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="browser">Browser:</label>
                        <input type="text" name="browser" required>
                    </div>
                    <button type="submit">Save</button>
                </form>
            </div>
        </div>

        <div id="delete-modal" class="modal">
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <form id="user-delete-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id">
                    <p>Are you sure you want to delete this user?</p>
                    <div class="btn-wrapper">
                        <button type="submit">Yes</button>
                        <button id="cancel-delete" class="btn-danger">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script>
        $(document).ready(function() {
            // Open delete modal
            $('.delete-user').on('click', function() {
                var userId = $(this).data('id');
                $('#user-delete-form input[name="id"]').val(userId);
                $('#delete-modal').show();
            });

            // Close delete modal
            $('.modal-close, #cancel-delete').on('click', function() {
                $('#delete-modal').hide();
            });

            // Handle delete form submission
            $('#user-delete-form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.post('', formData, function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 200) {
                        toastr.success(result.message);
                        location.reload();
                    } else {
                        toastr.error(result.message);
                    }
                });
            });
        });
    </script>
    <script src="../../assets/script.js"></script>
</body>
</html>
