<?php
require_once __DIR__ . '/../base_controller.php';

class ProductController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function createProduct($name, $price, $description) {
        try {
            $stmt = $this->db->prepare("INSERT INTO products (name, price, description, user_id) VALUES (:name, :price, :description, :user_id)");
            $stmt->execute([
                ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                ':price' => $price,
                ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                ':user_id' => $_SESSION['user']['id']
            ]);
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getProduct($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute([':id' => (int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function updateProduct($id, $name, $price, $description) {
        try {
            $stmt = $this->db->prepare("UPDATE products SET name = :name, price = :price, description = :description WHERE id = :id");
            $stmt->execute([
                ':id' => (int)$id,
                ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                ':price' => $price,
                ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8')
            ]);
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function deleteProduct($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute([':id' => (int)$id]);
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getMyProducts() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $_SESSION['user']['id']]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getAllProducts() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            $userId = $_SESSION['user']['id'];

            $stmt = $this->db->prepare("SELECT * FROM products WHERE user_id != :user_id LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function searchProducts($keyword) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE name LIKE :keyword");
            $stmt->execute([':keyword' => '%' . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
