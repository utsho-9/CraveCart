<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/DatabaseConnection.php';
require_once __DIR__ . '/../Model/userModel.php';
require_once __DIR__ . '/../Model/orderModel.php';
require_once __DIR__ . '/../Model/feedbackModel.php';

function checkAdminAuth() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die("Unauthorized access");
    }
}

function adminIndex() {
    checkAdminAuth();
    $conn = openConnection();

    $users_result = getAllUsers($conn);
    $total_revenue = getTotalRevenue($conn);
    $payments_result = getUnpaidDeliveredOrders($conn);
    $feedback_result = getAllFeedback($conn);

    require_once __DIR__ . '/../View/admin_dash.php';
}

function adminCreateUser() {
    checkAdminAuth();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? '';
        $password = trim($_POST['password'] ?? '');

        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: index.php?action=admin_dash");
            exit();
        }

        $conn = openConnection();

        if (checkEmailExists($conn, $email)) {
            $_SESSION['error'] = "Email is already registered.";
            header("Location: index.php?action=admin_dash");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if (insertUser($conn, $name, $email, $hashed_password, $role)) {
            $_SESSION['success'] = "User created successfully.";
        } else {
            $_SESSION['error'] = "Failed to create user.";
        }
        header("Location: index.php?action=admin_dash");
        exit();
    }
}

function adminDeleteUser() {
    checkAdminAuth();
    header('Content-Type: application/json');
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $conn = openConnection();
        if (deleteUserById($conn, $id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Deletion failed']);
        }
        exit();
    }
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
    exit();
}

function adminSearchUsers() {
    checkAdminAuth();
    header('Content-Type: application/json');
    $search = $_GET['q'] ?? '';
    $conn = openConnection();
    $users = searchUsers($conn, $search);
    echo json_encode($users);
    exit();
}

function adminApprovePayment() {
    checkAdminAuth();
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $conn = openConnection();
        updatePaymentStatus($conn, $id, 'Paid');
        $_SESSION['success'] = "Payment approved.";
    }
    header("Location: index.php?action=admin_dash");
    exit();
}

function adminModerateFeedback() {
    checkAdminAuth();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['admin_action'])) {
        $id = (int)$_POST['id'];
        $action = $_POST['admin_action'];
        $conn = openConnection();
        updateFeedbackAction($conn, $id, $action);
        $_SESSION['success'] = "Feedback status updated.";
    }
    header("Location: index.php?action=admin_dash");
    exit();
}
?>
