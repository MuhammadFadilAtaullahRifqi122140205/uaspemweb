<?php
require_once __DIR__ . '/../base_controller.php';

class ProductController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    // Fungsi untuk membuat produk baru
    public function createProduct($name, $price, $description, $imagePath) {
        try {
            // Validasi input
            if (empty($name) || empty($price) || empty($description)) {
                return ['status' => 400, 'message' => 'All fields are required.'];
            }

            if (!is_numeric($price) || $price < 0) {
                return ['status' => 400, 'message' => 'Price must be a valid number and cannot be less than 0.'];
            }

            // Menyimpan produk ke database
            $stmt = $this->db->prepare("INSERT INTO products (name, price, description, image, user_id) VALUES (:name, :price, :description, :image, :user_id)");
            $stmt->execute([
                ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                ':price' => $price,
                ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                ':image' => $imagePath,
                ':user_id' => $_SESSION['user']['id']
            ]);
            return ['status' => 200, 'message' => 'OK'];
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk mendapatkan produk berdasarkan ID
    public function getProductById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute([':id' => (int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk memperbarui produk
    public function updateProduct($id, $name, $price, $description, $imagePath) {
        try {
            // Validasi input
            if (empty($name) || empty($price) || empty($description)) {
                return ['status' => 400, 'message' => 'All fields are required.'];
            }

            if (!is_numeric($price) || $price < 0) {
                return ['status' => 400, 'message' => 'Price must be a valid number and cannot be less than 0.'];
            }

            $product = $this->getProductById($id);

            // Hapus gambar lama jika ada gambar baru yang diunggah
            if ($product && !empty($imagePath)) {
                if (!empty($product['image'])) {
                    $existingImagePath = __DIR__ . '/../../../' . $product['image'];
                    if (file_exists($existingImagePath)) {
                        unlink($existingImagePath);
                    }
                }
            }

            // Cek apakah pengguna yang sedang login adalah pemilik produk atau admin
            if ($product['user_id'] !== $_SESSION['user']['id']) {
                if ($_SESSION['user']['role_id'] !== 1) {
                    return ['status' => 403, 'message' => 'Forbidden'];
                }
            }

            if ($imagePath === '') {
                $imagePath = $product['image'];
            }

            // Perbarui produk di database
            $stmt = $this->db->prepare("UPDATE products SET name = :name, price = :price, description = :description, image = :image WHERE id = :id");
            $stmt->execute([
                ':id' => (int)$id,
                ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                ':price' => $price,
                ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                ':image' => $imagePath
            ]);
            return ['status' => 200, 'message' => 'OK'];
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk menghapus produk
    public function deleteProduct($id) {
        try {
            $product = $this->getProductById($id);

            // Hapus gambar produk jika ada
            if ($product) {
                if (!empty($product['image'])) {
                    $existingImagePath = __DIR__ . '/../../..' . $product['image'];
                    if (file_exists($existingImagePath)) {
                        unlink($existingImagePath);
                    }
                }
            }

            // Cek apakah pengguna yang sedang login adalah pemilik produk atau admin
            if ($product['user_id'] !== $_SESSION['user']['id']) {
                if ($_SESSION['user']['role_id'] !== 1) {
                    return ['status' => 403, 'message' => 'Forbidden'];
                }
            }

            // Hapus produk dari database
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute([':id' => (int)$id]);
            return ['status' => 200, 'message' => 'OK'];
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk mendapatkan produk milik pengguna yang sedang login
    public function getMyProducts() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $_SESSION['user']['id']]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk mendapatkan semua produk
    public function getAllProducts() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            $userId = $_SESSION['user']['id'];
            $userRole = $_SESSION['user']['role_id'];

            // Jika pengguna adalah admin, dapatkan semua produk
            if ($userRole == 1) {
                $stmt = $this->db->prepare("
                    SELECT products.*, users.username
                    FROM products
                    JOIN users ON products.user_id = users.id
                    LIMIT :limit OFFSET :offset
                ");
            } else {
                // Jika pengguna bukan admin, dapatkan semua produk kecuali milik pengguna yang sedang login
                $stmt = $this->db->prepare("
                    SELECT products.*, users.username
                    FROM products
                    JOIN users ON products.user_id = users.id
                    WHERE products.user_id != :user_id
                    LIMIT :limit OFFSET :offset
                ");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            }

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['status' => 500, 'message' => "Error: " . $e->getMessage()];
        }
    }

    // Fungsi untuk menangani permintaan
    public function handleRequest() {
        $response = ['status' => 400, 'message' => 'Invalid request'];
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            return ['status' => 403, 'message' => 'Invalid CSRF token'];
        }

        $action = $_POST['action'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $imagePath = '';

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
            } else {
                return ['status' => 500, 'message' => 'There was an error moving the uploaded file.'];
            }
        }

        // Menangani aksi berdasarkan permintaan
        switch ($action) {
            case 'create':
                return $this->createProduct($name, $price, $description, $imagePath);
            case 'update':
                $id = $_POST['id'];
                return $this->updateProduct($id, $name, $price, $description, $imagePath);
            case 'delete':
                $id = $_POST['id'];
                return $this->deleteProduct($id);
            default:
                return ['status' => 400, 'message' => 'Invalid action'];
        }
    }
}
