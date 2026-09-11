<?php
require_once 'models/Database.php';

class AdminController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function index() {
        $users_result = mysqli_query($this->conn, "SELECT id, name, email, role FROM users ORDER BY id DESC");

        $rev_query = mysqli_query($this->conn, "SELECT SUM(total_price) as grand_total FROM orders WHERE payment_status = 'Paid'");
        $total_revenue = mysqli_fetch_assoc($rev_query)['grand_total'] ?? 0.00;

        $payments_result = mysqli_query($this->conn, "SELECT id, total_price, payment_status FROM orders WHERE status = 'Delivered' AND payment_status = 'Unpaid'");

        $feedback_result = mysqli_query($this->conn, "SELECT f.*, u.name FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");

        require_once 'views/admin_dash.php';
    }

    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim(mysqli_real_escape_string($this->conn, $_POST['name']));
            $email = trim(mysqli_real_escape_string($this->conn, $_POST['email']));
            $role = mysqli_real_escape_string($this->conn, $_POST['role']);
            $password = trim($_POST['password']);

            if (empty($name) || empty($email) || empty($password)) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: index.php?action=admin_dash");
                exit();
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password_hash, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
            mysqli_query($this->conn, $sql);

            $_SESSION['success'] = "User created successfully.";
            header("Location: index.php?action=admin_dash");
            exit();
        }
    }

    public function deleteUser() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if (mysqli_query($this->conn, "DELETE FROM users WHERE id = $id")) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            exit();
        }
    }

    public function searchUsers() {
        $search = isset($_GET['q']) ? mysqli_real_escape_string($this->conn, $_GET['q']) : '';
        $sql = "SELECT id, name, email, role FROM users WHERE name LIKE '%$search%' OR email LIKE '%$search%'";

        $result = mysqli_query($this->conn, $sql);
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        echo json_encode($users);
        exit();
    }

    public function approvePayment() {
        $id = (int)$_GET['id'];
        mysqli_query($this->conn, "UPDATE orders SET payment_status = 'Paid' WHERE id = $id");
        header("Location: index.php?action=admin_dash");
    }

    public function moderateFeedback() {
        $id = (int)$_POST['id'];
        $action = mysqli_real_escape_string($this->conn, $_POST['admin_action']);
        mysqli_query($this->conn, "UPDATE feedback SET admin_action = '$action' WHERE id = $id");
        header("Location: index.php?action=admin_dash");
    }
}
?>