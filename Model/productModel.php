<?php
require_once __DIR__ . '/DatabaseConnection.php';

function getAvailableProducts($conn) {
    $sql = "SELECT * FROM products WHERE is_available = 1 ORDER BY id DESC";
    return mysqli_query($conn, $sql);
}

function getAllProducts($conn) {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    return mysqli_query($conn, $sql);
}

function getProductById($conn, $id) {
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $product;
    }
    return null;
}

function insertProduct($conn, $name, $price, $stock, $image_path) {
    $stmt = mysqli_prepare($conn, "INSERT INTO products (name, price, stock_limit, image_path) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sdis", $name, $price, $stock, $image_path);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function deleteProductById($conn, $id) {
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function searchProducts($conn, $query) {
    $search = "%" . $query . "%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE name LIKE ? ORDER BY id DESC");
    $products = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $search);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    return $products;
}

function toggleProductAvailability($conn, $id) {
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "UPDATE products SET is_available = NOT is_available WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}
?>
