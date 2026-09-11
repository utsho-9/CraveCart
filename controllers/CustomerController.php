<?php
require_once 'models/Database.php';

class CustomerController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function index() {
        $customer_id = $_SESSION['user_id'] ?? null;
        if (!$customer_id) { header("Location: index.php"); exit(); }

        $menu_result = mysqli_query($this->conn, "SELECT * FROM products WHERE is_available = 1");

        $orders_result = mysqli_query($this->conn, "SELECT o.*, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.customer_id = $customer_id ORDER BY o.id DESC");

        $feedback_result = mysqli_query($this->conn, "SELECT * FROM feedback WHERE user_id = $customer_id ORDER BY created_at DESC");

        require_once 'views/customer_dash.php';
    }

    public function placeOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_SESSION['user_id'];
            $product_id = (int)$_POST['product_id'];
            $zone = mysqli_real_escape_string($this->conn, $_POST['zone']);
            $custom_request = mysqli_real_escape_string($this->conn, $_POST['custom_request']);
            $is_express = isset($_POST['is_express']) ? 1 : 0;

            if (empty($zone)) {
                $_SESSION['error'] = "Delivery zone is required.";
                header("Location: index.php?action=customer_dash");
                exit();
            }

            $price_query = mysqli_query($this->conn, "SELECT price FROM products WHERE id = $product_id");
            $base_price = mysqli_fetch_assoc($price_query)['price'];
            $total_price = $is_express ? $base_price + 5.00 : $base_price;

            $sql = "INSERT INTO orders (customer_id, product_id, custom_request, zone, is_express, total_price, status, payment_status) 
                    VALUES ($customer_id, $product_id, '$custom_request', '$zone', $is_express, $total_price, 'Pending', 'Unpaid')";

            mysqli_query($this->conn, $sql);
            $_SESSION['success'] = "Order placed successfully!";
            header("Location: index.php?action=customer_dash");
            exit();
        }
    }

    public function updateRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = (int)$_POST['order_id'];
            $new_request = mysqli_real_escape_string($this->conn, $_POST['custom_request']);

            mysqli_query($this->conn, "UPDATE orders SET custom_request = '$new_request' WHERE id = $order_id AND status = 'Pending'");
            $_SESSION['success'] = "Order request updated.";
            header("Location: index.php?action=customer_dash");
            exit();
        }
    }

    public function cancelOrder() {
        if (isset($_GET['id'])) {
            $order_id = (int)$_GET['id'];
            if (mysqli_query($this->conn, "DELETE FROM orders WHERE id = $order_id AND status = 'Pending'")) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            exit();
        }
    }

    public function searchMenu() {
        $search = isset($_GET['q']) ? mysqli_real_escape_string($this->conn, $_GET['q']) : '';
        $sql = "SELECT * FROM products WHERE is_available = 1 AND name LIKE '%$search%'";
        $result = mysqli_query($this->conn, $sql);

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        echo json_encode($products);
        exit();
    }

    public function submitFeedback() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_SESSION['user_id'];
            $message = trim(mysqli_real_escape_string($this->conn, $_POST['message']));

            if (!empty($message)) {
                mysqli_query($this->conn, "INSERT INTO feedback (user_id, message) VALUES ($customer_id, '$message')");
                $_SESSION['success'] = "Feedback submitted!";
            }
            header("Location: index.php?action=customer_dash");
            exit();
        }
    }
}
?>