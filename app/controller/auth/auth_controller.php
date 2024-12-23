<?php
require_once __DIR__ . '/../base_controller.php';

class AuthController extends BaseController {
    public function __construct() {
        parent::__construct();
    }


    public function handleRequest($action, $data) {
        // Verifikasi CSRF token
        if (!$this->verifyCsrfToken($data['csrf_token'])) {
            echo "Invalid CSRF token.";
            return;
        }

        // Escape input data
        $username = htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
        $gender = htmlspecialchars($data['gender'], ENT_QUOTES, 'UTF-8');
        $city = htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8');
        $agree = isset($data['agree']);
        // Mendapatkan Public IPV4 address dari user
        $ip = file_get_contents('https://api.ipify.org');
        $browser = htmlspecialchars($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');

        if ($action === 'register' && $agree) {
            // Jika query string action = register (?action=register) maka akan memanggil method register
            $result = $this->register($username, $password, $gender, $city, $ip, $browser);
            if ($result === true) {
                echo "Registration succeeded.";
            } else {
                echo $result;
            }
        } elseif ($action === 'login') {
            // Jika query string action = login (?action=login) maka akan memanggil method login
            $result = $this->login($username, $password);
            if ($result === "admin") {
                echo "Admin";
            }else if($result === "user"){
                echo "User";
            } else {
                echo $result;
            }
        } elseif ($action === 'logout') {
            // Jika query string action = logout (?action=logout) maka akan memanggil method logout
            $this->logout();
        }
    }

    public function login($username, $password) {
        //  Validasi login request
        $validationResult = $this->validateLogin($username, $password);
        if ($validationResult !== true) {
            //  Jika validasi gagal maka akan mengembalikan pesan error
            return $validationResult;
        }

        // Query untuk mendapatkan user berdasarkan username
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        try {
            // Eksekusi query
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Jika user ditemukan dan password sesuai maka akan membuat session logged_in dan user
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user'] = $user;
                // Jika role_id dari user adalah 1 maka akan mengembalikan "admin" jika tidak maka akan mengembalikan "user"
                return $user['role_id'] === 1 ? "admin" : "user";
            } else {
                // Jika user tidak ditemukan atau password tidak sesuai maka akan mengembalikan pesan error "Login failed. Invalid credentials."
                return "Login failed. Invalid credentials.";
            }
        } catch (PDOException $e) {
            // Jika terjadi error saat eksekusi query maka akan mengembalikan pesan error
            return "Login failed: " . $e->getMessage();
        }
    }

    public function logout() {
        // Menghapus session logged_in dan user
        session_unset();
        session_destroy();
        // Redirect ke halaman utama
        header("Location: " . getenv('APP_URL'));
    }

    public function register($username, $password, $gender, $city, $ip, $browser) {
        // Validasi register request
        $validationResult = $this->validateRegistration($username, $password, $gender, $city, $ip, $browser);
        if ($validationResult !== true) {
            // Jika validasi gagal maka akan mengembalikan pesan error
            return $validationResult;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Query untuk insert user baru
        $stmt = $this->db->prepare("INSERT INTO users (username, password, gender, city, ip_address, browser) VALUES (:username, :password, :gender, :city, :ip, :browser)");
        try {
            // Eksekusi query
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':gender' => $gender,
                ':city' => $city,
                ':ip' => $ip,
                ':browser' => $browser
            ]);
            // Jika user berhasil diinsert maka akan mengembalikan true yang akan di handle di front-end
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                // Code error 23000 adalah error ketika terjadi duplicate entry
                $errorMessage = $e->getMessage();

                if (strpos($errorMessage, 'for key \'username\'') !== false) {
                    // Jika error message mengandung kata 'username' maka akan mengembalikan pesan error "Registration failed: Username already exists."
                    return "Registration failed: Username already exists.";
                } elseif (strpos($errorMessage, 'for key \'ip_address\'') !== false) {
                    // Jika error message mengandung kata 'ip_address' maka akan mengembalikan pesan error "Registration failed: User with this IP address already exists."
                    return "Registration failed: User with this IP address already exists.";
                } else {
                    // Default duplikat entry
                    return "Registration failed: Duplicate entry.";
                }
            }
            // Jika terjadi error lainnya maka akan mengembalikan pesan error
            return "Registration failed: " . $e->getMessage();
        }
    }

    private function validateLogin($username, $password) {
        // request->username dan request->password tidak boleh kosong
        if (empty($username)) {
            return "Username is required.";
        }
        if (empty($password)) {
            return "Password is required.";
        }
        // Jika validasi berhasil maka akan mengembalikan true
        return true;
    }

    private function validateRegistration($username, $password, $gender, $city, $ip, $browser) {
        // request->username, request->password, request->gender, request->city, request->ip_address,
        // request->browser tidak boleh kosong
        if (empty($username)) {
            return "Username is required.";
        }
        if (empty($password)) {
            return "Password is required.";
        }
        if (empty($gender)) {
            return "Gender is required.";
        }
        if (empty($city)) {
            return "City is required.";
        }
        if (empty($ip)) {
            return "IP address is required.";
        }
        if (empty($browser)) {
            return "Browser information is required.";
        }
        // Jika validasi berhasil maka akan mengembalikan true
        return true;
    }
}
