<?php
require_once __DIR__ . '/DatabaseConnection.php';

function createOrder($conn, $customer_id, $product_id, $custom_request, $zone, $is_express, $total_price) {
    $stmt = mysqli_prepare($conn, "INSERT INTO orders (customer_id, product_id, custom_request, zone, is_express, total_price, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending', 'Unpaid')");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iissid", $customer_id, $product_id, $custom_request, $zone, $is_express, $total_price);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function getCustomerOrders($conn, $customer_id) {
    $customer_id = (int)$customer_id;
    $stmt = mysqli_prepare($conn, "SELECT o.*, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.customer_id = ? ORDER BY o.id DESC");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $customer_id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
    return false;
}

function updateOrderRequest($conn, $order_id, $new_request) {
    $order_id = (int)$order_id;
    $stmt = mysqli_prepare($conn, "UPDATE orders SET custom_request = ? WHERE id = ? AND status = 'Pending'");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $new_request, $order_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function cancelOrderById($conn, $order_id) {
    $order_id = (int)$order_id;
    $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE id = ? AND status = 'Pending'");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function getKitchenOrders($conn) {
    $sql = "SELECT o.id, o.custom_request, o.is_express, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.status = 'Pending' ORDER BY o.id ASC";
    return mysqli_query($conn, $sql);
}

function updateOrderStatus($conn, $order_id, $status) {
    $order_id = (int)$order_id;
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function getDeliveryOrders($conn) {
    $sql = "SELECT o.*, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.status IN ('Preparing', 'Out for Delivery') ORDER BY o.is_express DESC, o.id ASC";
    return mysqli_query($conn, $sql);
}

function searchOrdersByZone($conn, $query) {
    $search = "%" . $query . "%";
    $stmt = mysqli_prepare($conn, "SELECT o.*, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.status IN ('Preparing', 'Out for Delivery') AND o.zone LIKE ? ORDER BY o.is_express DESC");
    $orders = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $search);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    return $orders;
}

function updatePaymentStatus($conn, $order_id, $status) {
    $order_id = (int)$order_id;
    $stmt = mysqli_prepare($conn, "UPDATE orders SET payment_status = ? WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function getTotalRevenue($conn) {
    $sql = "SELECT SUM(total_price) as grand_total FROM orders WHERE payment_status = 'Paid'";
    $query = mysqli_query($conn, $sql);
    if ($query && $row = mysqli_fetch_assoc($query)) {
        return $row['grand_total'] ?? 0.00;
    }
    return 0.00;
}

function getUnpaidDeliveredOrders($conn) {
    $sql = "SELECT id, total_price, payment_status FROM orders WHERE status = 'Delivered' AND payment_status = 'Unpaid' ORDER BY id DESC";
    return mysqli_query($conn, $sql);
}
?>
