<?php
require_once __DIR__ . '/DatabaseConnection.php';

function insertFeedback($conn, $user_id, $message) {
    $stmt = mysqli_prepare($conn, "INSERT INTO feedback (user_id, message) VALUES (?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "is", $user_id, $message);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function getAllFeedback($conn) {
    $sql = "SELECT f.*, u.name FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC";
    return mysqli_query($conn, $sql);
}

function getCustomerFeedback($conn, $user_id) {
    $user_id = (int)$user_id;
    $stmt = mysqli_prepare($conn, "SELECT * FROM feedback WHERE user_id = ? ORDER BY created_at DESC");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
    return false;
}

function updateFeedbackAction($conn, $id, $action) {
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "UPDATE feedback SET admin_action = ? WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $action, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}
?>
