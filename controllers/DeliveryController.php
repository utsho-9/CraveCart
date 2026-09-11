<?php
require_once 'models/Database.php';

class DeliveryController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function index() {
        $orders_result = mysqli_query($this->conn, "SELECT o.*, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.status IN ('Preparing', 'Out for Delivery') ORDER BY o.is_express DESC, o.id ASC");

        $notes_result = mysqli_query($this->conn, "SELECT * FROM delivery_notes ORDER BY created_at DESC");

        require_once 'views/delivery_dash.php';
    }

    public function addNote() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = (int)$_POST['order_id'];
            $note = trim(mysqli_real_escape_string($this->conn, $_POST['note']));

            if (empty($note) || $order_id <= 0) {
                $_SESSION['error'] = "Note cannot be empty and must be linked to an order.";
                header("Location: index.php?action=delivery_dash");
                exit();
            }

            mysqli_query($this->conn, "INSERT INTO delivery_notes (order_id, note) VALUES ($order_id, '$note')");
            $_SESSION['success'] = "Delivery note added.";
            header("Location: index.php?action=delivery_dash");
            exit();
        }
    }

    public function deleteNote() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if (mysqli_query($this->conn, "DELETE FROM delivery_notes WHERE id = $id")) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            exit();
        }
    }

    public function searchByZone() {
        $search = isset($_GET['q']) ? mysqli_real_escape_string($this->conn, $_GET['q']) : '';
        $sql = "SELECT o.*, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.status IN ('Preparing', 'Out for Delivery') AND o.zone LIKE '%$search%' ORDER BY o.is_express DESC";

        $result = mysqli_query($this->conn, $sql);
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        echo json_encode($orders);
        exit();
    }

    public function updateDeliveryStatus() {
        $id = (int)$_GET['id'];
        $new_status = mysqli_real_escape_string($this->conn, $_GET['status']);
        mysqli_query($this->conn, "UPDATE orders SET status = '$new_status' WHERE id = $id");
        header("Location: index.php?action=delivery_dash");
    }

    public function collectCash() {
        $id = (int)$_GET['id'];
        mysqli_query($this->conn, "UPDATE orders SET payment_status = 'Paid' WHERE id = $id");
        header("Location: index.php?action=delivery_dash");
    }
}
?>