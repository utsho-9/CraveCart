<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/DatabaseConnection.php';
require_once __DIR__ . '/../Model/productModel.php';
require_once __DIR__ . '/../Model/orderModel.php';
require_once __DIR__ . '/../Model/feedbackModel.php';

function checkCustomerAuth() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
        die("Unauthorized access");
    }
}

function customerIndex() {
    checkCustomerAuth();
    $customer_id = $_SESSION['user_id'] ?? null;
    if (!$customer_id) {
        header("Location: index.php");
        exit();
    }

    $conn = openConnection();
    $menu_result = getAvailableProducts($conn);
    $orders_result = getCustomerOrders($conn, $customer_id);
    $feedback_result = getCustomerFeedback($conn, $customer_id);

    require_once __DIR__ . '/../View/customer_dash.php';
}

function customerPlaceOrder() {
    checkCustomerAuth();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customer_id = $_SESSION['user_id'];
        $product_id = (int)($_POST['product_id'] ?? 0);
        $zone = trim($_POST['zone'] ?? '');
        $custom_request = trim($_POST['custom_request'] ?? '');
        $is_express = isset($_POST['is_express']) ? 1 : 0;

        if (empty($zone) || $product_id <= 0) {
            $_SESSION['error'] = "Delivery zone is required.";
            header("Location: index.php?action=customer_dash");
            exit();
        }

        $conn = openConnection();
        $product = getProductById($conn, $product_id);
        if (!$product) {
            $_SESSION['error'] = "Selected item is not available.";
            header("Location: index.php?action=customer_dash");
            exit();
        }

        $base_price = (float)$product['price'];
        $total_price = $is_express ? $base_price + 5.00 : $base_price;

        if (createOrder($conn, $customer_id, $product_id, $custom_request, $zone, $is_express, $total_price)) {
            $_SESSION['success'] = "Order placed successfully!";
        } else {
            $_SESSION['error'] = "Failed to place order.";
        }
        header("Location: index.php?action=customer_dash");
        exit();
    }
}

function customerUpdateRequest() {
    checkCustomerAuth();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $order_id = (int)($_POST['order_id'] ?? 0);
        $new_request = trim($_POST['custom_request'] ?? '');
        $conn = openConnection();

        if (updateOrderRequest($conn, $order_id, $new_request)) {
            $_SESSION['success'] = "Order request updated.";
        } else {
            $_SESSION['error'] = "Failed to update order request.";
        }
        header("Location: index.php?action=customer_dash");
        exit();
    }
}

function customerCancelOrder() {
    checkCustomerAuth();
    header('Content-Type: application/json');
    if (isset($_GET['id'])) {
        $order_id = (int)$_GET['id'];
        $conn = openConnection();
        if (cancelOrderById($conn, $order_id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cannot cancel order']);
        }
        exit();
    }
    echo json_encode(['status' => 'error', 'message' => 'No order ID']);
    exit();
}

function customerSearchMenu() {
    checkCustomerAuth();
    header('Content-Type: application/json');
    $search = $_GET['q'] ?? '';
    $conn = openConnection();
    $products = searchProducts($conn, $search);
    echo json_encode($products);
    exit();
}

function customerSubmitFeedback() {
    checkCustomerAuth();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customer_id = $_SESSION['user_id'];
        $message = trim($_POST['message'] ?? '');

        if (!empty($message)) {
            $conn = openConnection();
            insertFeedback($conn, $customer_id, $message);
            $_SESSION['success'] = "Feedback submitted!";
        } else {
            $_SESSION['error'] = "Feedback cannot be empty.";
        }
        header("Location: index.php?action=customer_dash");
        exit();
    }
}
?>
