<?php
require_once __DIR__ . '/../base_controller.php';
class UserController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    // Fungsi untuk membuat pengguna baru dari admin
    public function createUserFromAdmin($username, $gender, $city, $ip, $browser) {
        try {
            $password = password_hash('password', PASSWORD_DEFAULT); // Hash password default
            $stmt = $this->db->prepare("INSERT INTO users (username, gender, city, ip_address, browser, password) VALUES (:username, :gender, :city, :ip, :browser, :password)");
            $stmt->execute([
                ':username' => $username,
                ':gender' => $gender,
                ':city' => $city,
                ':ip' => $ip,
                ':browser' => $browser,
                ':password' => $password
            ]);
            return ['status' => 200, 'message' => 'OK'];
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk membuat pengguna baru
    public function createUser($username, $gender, $city, $ip, $browser, $password) {
        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, gender, city, ip_address, browser) VALUES (:username, :gender, :city, :ip, :browser)");
            $stmt->execute([
                ':username' => $username,
                ':gender' => $gender,
                ':city' => $city,
                ':ip' => $ip,
                ':browser' => $browser
            ]);
            return ['status' => 200, 'message' => 'OK'];
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk mendapatkan pengguna berdasarkan ID
    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => (int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk mendapatkan semua pengguna kecuali pengguna saat ini
    public function getAllUsers() {
        try {
            $currentUserId = $_SESSION['user']['id'];
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id != :currentUserId");
            $stmt->bindParam(':currentUserId', $currentUserId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk memperbarui pengguna
    public function updateUser($id, $username, $gender, $city, $ip, $browser) {
        try {
            $user = $this->getUserById($id);

            // Cek apakah pengguna yang sedang login adalah pengguna yang akan diperbarui atau admin
            if ($_SESSION['user']['id'] !== $user['id']) {
                if ($_SESSION['user']['role_id'] !== 1) {
                    return ['status' => 403, 'message' => 'Forbidden'];
                }
            }

            $stmt = $this->db->prepare("UPDATE users SET username = :username, gender = :gender, city = :city, ip_address = :ip, browser = :browser WHERE id = :id");
            $stmt->execute([
                ':id' => $id,
                ':username' => $username,
                ':gender' => $gender,
                ':city' => $city,
                ':ip' => $ip,
                ':browser' => $browser
            ]);
            return ['status' => 200, 'message' => 'OK'];
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk memperbarui profil pengguna
    public function updateUserProfile($id, $username, $gender, $city, $imagePath) {
        try {
            $escapedUsername = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
            $escapedGender = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
            $escapedCity = htmlspecialchars($city, ENT_QUOTES, 'UTF-8');
            $browser = $_SERVER['HTTP_USER_AGENT'];
            $ip = file_get_contents('https://api.ipify.org');

            $user = $this->getUserById($id);

            // Cek apakah pengguna yang sedang login adalah pengguna yang akan diperbarui atau admin
            if ($_SESSION['user']['id'] !== $user['id']) {
                if ($_SESSION['user']['role_id'] !== 1) {
                    return ['status' => 403, 'message' => 'Forbidden'];
                }
            }

            $imagePath = $user['image'];

            // Proses upload gambar
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size'];
                $fileType = $_FILES['image']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . '/../../../storage/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imagePath = '/storage/' . $newFileName;
                    $_SESSION['user']['image'] = $imagePath;
                } else {
                    return "There was an error moving the uploaded file.";
                }
            }

            $stmt = $this->db->prepare("UPDATE users SET username = :username, gender = :gender, city = :city, ip_address = :ip, browser = :browser, image = :image WHERE id = :id");
            $stmt->execute([
                ':id' => (int)$id,
                ':username' => $escapedUsername,
                ':gender' => $escapedGender,
                ':city' => $escapedCity,
                ':ip' => $ip,
                ':browser' => $browser,
                ':image' => $imagePath
            ]);

            // Perbarui sesi pengguna jika pengguna yang sedang login adalah pengguna yang diperbarui
            if ($_SESSION['user']['id'] === $id) {
                $_SESSION['user']['username'] = $escapedUsername;
                $_SESSION['user']['gender'] = $escapedGender;
                $_SESSION['user']['city'] = $escapedCity;
            }

            return ['status' => 200, 'message' => 'OK'];
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk menghapus pengguna
    public function deleteUser($id) {
        try {
            $user = $this->getUserById($id);
            // Cek apakah pengguna yang sedang login adalah pengguna yang akan dihapus atau admin
            if ($_SESSION['user']['id'] !== $user['id']) {
                if ($_SESSION['user']['role_id'] !== 1) {
                    return ['status' => 403, 'message' => 'Forbidden'];
                }
            }
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return ['status' => 200, 'message' => 'OK'];
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk menangani permintaan
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $response = ['status' => 400, 'message' => 'Invalid request'];
            if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
                $response = ['status' => 403, 'message' => 'Invalid CSRF token'];
            } else {
                $action = $_POST['action'];
                switch ($action) {
                    case 'create':
                        $response = $this->createUserFromAdmin(
                            $_POST['username'],
                            $_POST['gender'],
                            $_POST['city'],
                            $_POST['ip'],
                            $_POST['browser']
                        );
                        break;
                    case 'update':
                        $response = $this->updateUser(
                            $_POST['id'],
                            $_POST['username'],
                            $_POST['gender'],
                            $_POST['city'],
                            $_POST['ip'],
                            $_POST['browser']
                        );
                        break;
                    case 'delete':
                        $response = $this->deleteUser($_POST['id']);
                        break;
                }
            }
            echo json_encode($response);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $user = $this->getUserById($_GET['id']);
            echo json_encode($user);
            exit();
        }
    }
}
