<?php
require_once 'models/Database.php';

class RestaurantController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function index() {
        $products_result = mysqli_query($this->conn, "SELECT * FROM products ORDER BY id DESC");

        $orders_result = mysqli_query($this->conn, "SELECT o.id, o.custom_request, o.is_express, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.status = 'Pending'");

        require_once 'views/restaurant_dash.php';
    }

    public function createProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim(mysqli_real_escape_string($this->conn, $_POST['name']));
            $price = (float)$_POST['price'];
            $stock = (int)$_POST['stock_limit'];

            if (empty($name) || $price <= 0 || $stock < 0) {
                $_SESSION['error'] = "Invalid product data.";
                header("Location: index.php?action=restaurant_dash");
                exit();
            }

            $image_path = 'assets/uploads/default.png';
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'assets/uploads/';
                $file_name = time() . '_' . basename($_FILES['product_image']['name']);
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_path)) {
                    $image_path = $target_path;
                }
            }

            $sql = "INSERT INTO products (name, price, stock_limit, image_path) VALUES ('$name', $price, $stock, '$image_path')";
            mysqli_query($this->conn, $sql);

            $_SESSION['success'] = "Product added to menu!";
            header("Location: index.php?action=restaurant_dash");
            exit();
        }
    }

    public function deleteProduct() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if (mysqli_query($this->conn, "DELETE FROM products WHERE id = $id")) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            exit();
        }
    }

    public function searchProducts() {
        $search = isset($_GET['q']) ? mysqli_real_escape_string($this->conn, $_GET['q']) : '';
        $sql = "SELECT * FROM products WHERE name LIKE '%$search%'";

        $result = mysqli_query($this->conn, $sql);
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        echo json_encode($products);
        exit();
    }

    public function toggleAvailability() {
        $id = (int)$_GET['id'];
        mysqli_query($this->conn, "UPDATE products SET is_available = NOT is_available WHERE id = $id");
        header("Location: index.php?action=restaurant_dash");
    }

    public function markPreparing() {
        $id = (int)$_GET['id'];
        mysqli_query($this->conn, "UPDATE orders SET status = 'Preparing' WHERE id = $id");
        header("Location: index.php?action=restaurant_dash");
    }
}
?>