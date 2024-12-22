<?php
require_once __DIR__ . '/../base_controller.php';

class AuthController extends BaseController {
    private $redis;

    public function __construct() {
        parent::__construct();
        $this->initRedisSession();
    }

    private function initRedisSession() {
        $this->redis = new Redis();
        $this->redis->connect(getenv("REDIS_HOST"), getenv("REDIS_PORT"));

        $sessionPrefix = "PHPREDIS_SESSION:";
        session_set_save_handler(
            function ($savePath, $sessionName) {
                return true;
            },
            // Close handler
            function () {
                return true;
            },
            // Read handler
            function ($sessionId) use ($sessionPrefix) {
                return $this->redis->get($sessionPrefix . $sessionId) ?: '';
            },
            // Write handler
            function ($sessionId, $sessionData) use ($sessionPrefix) {
                $lifetime = ini_get('session.gc_maxlifetime');
                return $this->redis->setex($sessionPrefix . $sessionId, $lifetime, $sessionData);
            },
            // Destroy handler
            function ($sessionId) use ($sessionPrefix) {
                return $this->redis->del($sessionPrefix . $sessionId) > 0;
            },
            // Garbage collection handler
            function ($maxLifetime) {
                return true;
            }
        );

        session_start();
    }


    public function handleRequest($action, $data) {
        if (!$this->verifyCsrfToken($data['csrf_token'])) {
            echo "Invalid CSRF token.";
            return;
        }

        $username = htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
        $gender = htmlspecialchars($data['gender'], ENT_QUOTES, 'UTF-8');
        $city = htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8');
        $agree = isset($data['agree']);
        $ip = file_get_contents('https://api.ipify.org');
        $browser = htmlspecialchars($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');

        if ($action === 'register' && $agree) {
            $result = $this->register($username, $password, $gender, $city, $ip, $browser);
            if ($result === true) {
                echo "Registration succeeded.";
            } else {
                echo $result;
            }
        } elseif ($action === 'login') {
            $result = $this->login($username, $password);
            if ($result === "admin") {
                echo "Admin";
            }else if($result === "user"){
                echo "User";
            } else {
                echo $result;
            }
        } elseif ($action === 'logout') {
            $this->logout();
        }
    }

    public function login($username, $password) {
        $validationResult = $this->validateLogin($username, $password);
        if ($validationResult !== true) {
            return $validationResult;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        try {
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user'] = $user;
                return $user['role_id'] === 1 ? "admin" : "user";
            } else {
                return "Login failed. Invalid credentials.";
            }
        } catch (PDOException $e) {
            return "Login failed: " . $e->getMessage();
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: " . getenv('APP_URL'));
    }

    public function register($username, $password, $gender, $city, $ip, $browser) {
        $validationResult = $this->validateRegistration($username, $password, $gender, $city, $ip, $browser);
        if ($validationResult !== true) {
            return $validationResult;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, gender, city, ip_address, browser) VALUES (:username, :password, :gender, :city, :ip, :browser)");
        try {
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':gender' => $gender,
                ':city' => $city,
                ':ip' => $ip,
                ':browser' => $browser
            ]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorMessage = $e->getMessage();

                if (strpos($errorMessage, 'for key \'username\'') !== false) {
                    return "Registration failed: Username already exists.";
                } elseif (strpos($errorMessage, 'for key \'ip_address\'') !== false) {
                    return "Registration failed: User with this IP address already exists.";
                } else {
                    return "Registration failed: Duplicate entry.";
                }
            }
            return "Registration failed: " . $e->getMessage();
        }
    }

    private function validateLogin($username, $password) {
        if (empty($username)) {
            return "Username is required.";
        }
        if (empty($password)) {
            return "Password is required.";
        }
        return true;
    }

    private function validateRegistration($username, $password, $gender, $city, $ip, $browser) {
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
        return true;
    }
}
