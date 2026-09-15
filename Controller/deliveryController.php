<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/DatabaseConnection.php';
require_once __DIR__ . '/../Model/orderModel.php';
require_once __DIR__ . '/../Model/noteModel.php';

function checkDeliveryAuth() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delivery') {
        die("Unauthorized access");
    }
}

function deliveryIndex() {
    checkDeliveryAuth();
    $conn = openConnection();

    $orders_result = getDeliveryOrders($conn);
    $notes_result = getAllDeliveryNotes($conn);

    require_once __DIR__ . '/../View/delivery_dash.php';
}

function deliveryAddNote() {
    checkDeliveryAuth();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $order_id = (int)($_POST['order_id'] ?? 0);
        $note = trim($_POST['note'] ?? '');

        if (empty($note) || $order_id <= 0) {
            $_SESSION['error'] = "Note cannot be empty and must be linked to an order.";
            header("Location: index.php?action=delivery_dash");
            exit();
        }

        $conn = openConnection();
        if (insertDeliveryNote($conn, $order_id, $note)) {
            $_SESSION['success'] = "Delivery note added.";
        } else {
            $_SESSION['error'] = "Failed to add delivery note.";
        }
        header("Location: index.php?action=delivery_dash");
        exit();
    }
}

function deliveryDeleteNote() {
    checkDeliveryAuth();
    header('Content-Type: application/json');
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $conn = openConnection();
        if (deleteDeliveryNoteById($conn, $id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete note']);
        }
        exit();
    }
    echo json_encode(['status' => 'error', 'message' => 'No note ID']);
    exit();
}

function deliverySearchByZone() {
    checkDeliveryAuth();
    header('Content-Type: application/json');
    $search = $_GET['q'] ?? '';
    $conn = openConnection();
    $orders = searchOrdersByZone($conn, $search);
    echo json_encode($orders);
    exit();
}

function deliveryUpdateStatus() {
    checkDeliveryAuth();
    if (isset($_GET['id'], $_GET['status'])) {
        $id = (int)$_GET['id'];
        $status = $_GET['status'];
        $allowed_statuses = ['Preparing', 'Out for Delivery', 'Delivered'];

        if (in_array($status, $allowed_statuses)) {
            $conn = openConnection();
            updateOrderStatus($conn, $id, $status);
            $_SESSION['success'] = "Order status updated to $status.";
        }
    }
    header("Location: index.php?action=delivery_dash");
    exit();
}

function deliveryCollectCash() {
    checkDeliveryAuth();
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $conn = openConnection();
        updatePaymentStatus($conn, $id, 'Paid');
        $_SESSION['success'] = "Cash collected and payment marked as Paid.";
    }
    header("Location: index.php?action=delivery_dash");
    exit();
}
?>
