<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/DatabaseConnection.php';
require_once __DIR__ . '/../Model/productModel.php';
require_once __DIR__ . '/../Model/orderModel.php';

function checkRestaurantAuth() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant') {
        die("Unauthorized access");
    }
}

function restaurantIndex() {
    checkRestaurantAuth();
    $conn = openConnection();

    $products_result = getAllProducts($conn);
    $orders_result = getKitchenOrders($conn);

    require_once __DIR__ . '/../View/restaurant_dash.php';
}

function restaurantCreateProduct() {
    checkRestaurantAuth();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock_limit'] ?? 0);

        if (empty($name) || $price <= 0 || $stock < 0) {
            $_SESSION['error'] = "Invalid product data.";
            header("Location: index.php?action=restaurant_dash");
            exit();
        }

        $image_path = 'assets/uploads/default.png';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_tmp = $_FILES['product_image']['tmp_name'];
            $file_name = basename($_FILES['product_image']['name']);
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($ext, $allowed_exts)) {
                $safe_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file_name);
                $target_path = $upload_dir . $safe_filename;
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $image_path = 'assets/uploads/' . $safe_filename;
                }
            }
        }

        $conn = openConnection();
        if (insertProduct($conn, $name, $price, $stock, $image_path)) {
            $_SESSION['success'] = "Product added to menu!";
        } else {
            $_SESSION['error'] = "Failed to add product.";
        }
        header("Location: index.php?action=restaurant_dash");
        exit();
    }
}

function restaurantDeleteProduct() {
    checkRestaurantAuth();
    header('Content-Type: application/json');
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $conn = openConnection();
        if (deleteProductById($conn, $id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
        }
        exit();
    }
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
    exit();
}

function restaurantSearchProducts() {
    checkRestaurantAuth();
    header('Content-Type: application/json');
    $search = $_GET['q'] ?? '';
    $conn = openConnection();
    $products = searchProducts($conn, $search);
    echo json_encode($products);
    exit();
}

function restaurantToggleAvailability() {
    checkRestaurantAuth();
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $conn = openConnection();
        toggleProductAvailability($conn, $id);
        $_SESSION['success'] = "Product availability updated.";
    }
    header("Location: index.php?action=restaurant_dash");
    exit();
}

function restaurantMarkPreparing() {
    checkRestaurantAuth();
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $conn = openConnection();
        updateOrderStatus($conn, $id, 'Preparing');
        $_SESSION['success'] = "Order is now preparing.";
    }
    header("Location: index.php?action=restaurant_dash");
    exit();
}
?>
