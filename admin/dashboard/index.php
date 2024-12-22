<?php
require_once __DIR__ . '/../../app/controller/user/user_controller.php';

$userController = new UserController();
$csrfToken = $userController->generateCsrfToken();
$userController->handleRequest();

$users = $userController->getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../../assets/style.css">
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
            <div class="cards">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                            <p>Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
                            <p>City: <?php echo htmlspecialchars($user['city']); ?></p>
                            <p>IP Address: <?php echo htmlspecialchars($user['ip_address']); ?></p>
                            <p>Browser: <?php echo htmlspecialchars($user['browser']); ?></p>
                            <div class="btn-wrapper">
                                <button class="edit-user" data-id="<?php echo $user['id']; ?>">Edit</button>
                                <button class="delete-user" data-id="<?php echo $user['id']; ?>">Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
            </div>
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
