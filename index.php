<?php
require_once __DIR__ . '/app/middleware/guest_middleware.php';
require_once __DIR__ . '/app/controller/auth/auth_controller.php';

$authController = new AuthController();
$crsfToken = $authController->generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAS Pemrograman Web</title>
    <link rel="stylesheet" href="<?php echo getenv('APP_URL') . '/assets/style.css'; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const APP_URL = "<?php echo getenv('APP_URL'); ?>";
    </script>
</head>
<body>
    <div class="body-wrapper">
        <div class="wrapper" style="display: none;">
            <h1>Register</h1>
            <form id="userForm" method="POST" action="<?php echo getenv('APP_URL') . '/auth.php?action=register'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $crsfToken; ?>">
                <label for="username">Nama Pengguna:</label>
                <div class="input-wrapper username-wrapper">
                    <input type="text" id="username" name="username" required><br>
                </div>
                <label for="password">Password:</label>
                <div class="input-wrapper password-wrapper">
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password"><i class="fa-solid fa-eye icon-password"></i></span>
                </div>
                <label for="password">Confirm Password:</label>
                <div class="input-wrapper password-confirm-wrapper">
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <span class="toggle-confirm-password"><i class="fa-solid fa-eye icon-confirm-password"></i></span>
                </div>
                <br>

                <label>Jenis Kelamin:</label>
                <div class="radio"></div>
                <div class="radio-group">
                    <div>
                        <input type="radio" id="male" name="gender" value="Male" required>
                        <label for="male">Laki-Laki</label>
                    </div>
                    <div>
                        <input type="radio" id="female" name="gender" value="Female" required>
                        <label for="female">Perempuan</label>
                    </div>
                </div>
                <br>
                <div class="input-wrapper">
                    <label for="city">Kota:</label>
                    <select id="city" name="city" required>
                        <option value="Bandar Lampung">Bandar Lampung</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" id="other_city" name="other_city" placeholder="Enter your city" style="display: none; margin-top: 10px;">
                </div>
                <br>

                <label class="agree">
                    <input type="checkbox" id="agree" name="agree" required> Setuju dengan syarat dan ketentuan
                </label><br>

                <div id="error-message"></div>
                <div class="change-form">
                    <p>Have an account? <span class="change">Login</span></p>
                </div>
                <button type="submit">Register</button>
            </form>
        </div>

        <div class="wrapper">
            <h1>Login</h1>
            <form id="loginForm" method="POST" action="<?php echo getenv('APP_URL') . '/auth.php?action=login'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $crsfToken; ?>">
                <label for="login-username">Nama Pengguna:</label>
                <div class="input-wrapper">
                    <input type="text" id="login-username" name="username" required><br>
                </div>
                <label for="login-password">Password:</label>
                <div class="input-wrapper">
                    <input type="password" id="login-password" name="password" required>
                    <span class="toggle-password"><i class="fa-solid fa-eye icon-password"></i></span>
                </div>
                <br>

                <div class="login-error-message"></div>
                <div class="change-form">
                    <p>Don't have an account? <span class="change">Register</span></p>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Toggle icon mata untuk input password
            $(".toggle-password").on("click", function () {
                var passwordField = $(this).siblings("input");
                var toggleIcon = $(".icon-password");

                if (passwordField.attr("type") === "password") {
                    passwordField.attr("type", "text");
                    toggleIcon.removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    passwordField.attr("type", "password");
                    toggleIcon.removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });

            // Toggle icon mata untuk input confirm password
            $(".toggle-confirm-password").on("click", function () {
                var passwordField = $(this).siblings("input");
                var toggleIcon = $(".icon-confirm-password");

                if (passwordField.attr("type") === "password") {
                    passwordField.attr("type", "text");
                    toggleIcon.removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    passwordField.attr("type", "password");
                    toggleIcon.removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });

            // Toggle form register dan login ketika tombol "Register" atau "Login" diklik style="display: none;
            $(".change").on("click", function() {
                $(".wrapper").toggle();
            });

            // Menampilkan input "other_city" ketika option "Others" dipilih
            $('#city').on('change', function() {
                if ($(this).val() === 'Others') {
                    $('#other_city').show();
                } else {
                    $('#other_city').hide();
                }
            });

            // Fungsi untuk validasi form
            function validateField(element) {
                var value = element.val().trim();
                var errorMessage = "";

                // Ketika input required dan value kosong maka menampilkan pesan error "Please fill out this field."
                if (element.attr("required") && value === "") {
                    errorMessage = "Please fill out this field.";
                }

                // Ketika username kurang dari 3 karakter maka menampilkan pesan error "Username must be at least 3 characters."
                if (element.attr("name") === "username" && value.length < 3) {
                    errorMessage = "Username must be at least 3 characters.";
                }

                // Ketika password kurang dari 8 karakter maka menampilkan pesan error "Password must be at least 8 characters."
                if (element.attr("name") === "password" && value.length < 8) {
                    errorMessage = "Password must be at least 8 characters.";
                }

                // Ketika password tidak mengandung huruf besar dan special character maka menampilkan pesan error
                // "Password must contain at least one uppercase letter and one special character."
                if (element.attr("name") === "password" && !/^(?=.*[A-Z])(?=.*[!@#$%^&*])/.test(value)) {
                    errorMessage = "Password must contain at least one uppercase letter and one special character.";
                }

                // Ketika confirm password tidak sama dengan password maka menampilkan pesan error "Passwords do not match."
                if (element.attr("name") === "confirmPassword" && value !== $("#password").val()) {
                    errorMessage = "Passwords do not match.";
                }

                // Ketika city "Others" dipilih dan input "other_city" kosong maka menampilkan pesan error "Please enter your city."
                if (element.attr("name") === "other_city" && $("#city").val() === "Others" && value === "") {
                    errorMessage = "Please enter your city.";
                }

                // Ketika checkbox "agree" tidak dicheck maka menampilkan pesan error "Please check this to proceed."
                if (element.attr("name") === "agree" && !element.is(":checked")) {
                    errorMessage = "Please check this to proceed.";
                }

                // Tampilkan error message di class "error-message" jika errorMessage tidak kosong
                if (errorMessage) {
                    element.next(".error-message").remove();
                    element.after('<span class="error-message">' + errorMessage + '</span>');
                    return false;
                } else {
                    // Hapus error message jika errorMessage kosong
                    element.next(".error-message").remove();
                    return true;
                }
            }

            // Validasi form ketika input atau select diubah
            $("#userForm input, #userForm select").on("input change", function() {
                validateField($(this));
            });

            // Event listener tambahan untuk blur
            $("#userForm input, #userForm select").on("blur", function() {
                validateField($(this));
            });

            // Validasi register form ketika form di submit
            $("#userForm").on("submit", function(e) {
                e.preventDefault();
                var isValid = true;
                $(this).find("input, select").each(function() {
                    if (!validateField($(this))) {
                        isValid = false;
                    }
                });

                // Post request jika form valid dan tampilkan pesan error jika gagal
                if (isValid) {
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.trim() === "Registration succeeded.") {
                                $(".wrapper").toggle();
                            } else {
                                $("#error-message").text(response);
                            }
                        }
                    });
                }
            });

            // Login form
            $("#loginForm").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.trim() === "User") {
                            window.location.href = APP_URL + '/user/dashboard';
                        }else if(response.trim() === "Admin") {
                            window.location.href = APP_URL + '/admin/dashboard';
                        } else {
                            $(".login-error-message").text(response);
                        }
                    }
                });
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" integrity="sha512-b+nQTCdtTBIRIbraqNEwsjB6UvL3UEMkXnhzd8awtCYh0Kcsjl9uEgwVFVbhoj3uu1DO1ZMacNvLoyJJiNfcvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
