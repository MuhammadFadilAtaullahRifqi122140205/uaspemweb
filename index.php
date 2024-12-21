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
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
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

                <label for="city">Kota:</label>
                <select id="city" name="city" required>
                    <option value="Bandar Lampung">Bandar Lampung</option>
                    <option value="Jakarta">Jakarta</option>
                    <option value="Surabaya">Surabaya</option>
                </select><br>

                <label class="agree">
                    <input type="checkbox" id="agree" name="agree" required> Setuju dengan syarat dan ketentuan
                </label><br>

                <div id="error-message" style="color: red;"></div>
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

                <div id="login-error-message" style="color: red;"></div>
                <div class="change-form">
                    <p>Don't have an account? <span class="change">Register</span></p>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
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

            $(".change").on("click", function() {
                $(".wrapper").toggle();
            });

            $.validator.addMethod("passwordPattern", function(value, element) {
                return this.optional(element) || /^(?=.*[A-Z])(?=.*[!@#$%^&*])/.test(value);
            }, "Password must contain at least one uppercase letter and one special character");

            $("#userForm").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 3
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        passwordPattern: true
                    },
                    gender: "required",
                    city: "required",
                    agree: "required"
                },
                messages: {
                    username: {
                        required: "Please enter your username",
                        minlength: "Username must be at least 3 characters"
                    },
                    password: {
                        required: "Please enter your password",
                        minlength: "Password must be at least 8 characters",
                        passwordPattern: "Password must contain at least one uppercase letter and one special character"
                    },
                    gender: "Please select your gender",
                    city: "Please select your city",
                    agree: "You must agree to the terms and conditions"
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "gender") {
                        error.insertAfter(".radio");
                    } else if (element.attr("name") == "agree") {
                        error.insertAfter(".agree");
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: $(form).serialize(),
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

            $("#loginForm").validate({
                rules: {
                    username: "required",
                    password: "required"
                },
                messages: {
                    username: "Please enter your username",
                    password: "Please enter your password"
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.trim() === "Login succeeded.") {
                                window.location.href = APP_URL + "/user/dashboard";
                            } else {
                                $("#login-error-message").text(response);
                            }
                        }
                    });
                }
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" integrity="sha512-b+nQTCdtTBIRIbraqNEwsjB6UvL3UEMkXnhzd8awtCYh0Kcsjl9uEgwVFVbhoj3uu1DO1ZMacNvLoyJJiNfcvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
