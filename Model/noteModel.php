<?php
require_once __DIR__ . '/DatabaseConnection.php';

function insertDeliveryNote($conn, $order_id, $note) {
    $stmt = mysqli_prepare($conn, "INSERT INTO delivery_notes (order_id, note) VALUES (?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "is", $order_id, $note);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function getAllDeliveryNotes($conn) {
    $sql = "SELECT * FROM delivery_notes ORDER BY created_at DESC";
    return mysqli_query($conn, $sql);
}

function deleteDeliveryNoteById($conn, $id) {
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "DELETE FROM delivery_notes WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}
?>
