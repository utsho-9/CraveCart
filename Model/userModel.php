<?php
require_once __DIR__ . '/DatabaseConnection.php';

function getUserByEmail($conn, $email) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $user;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

function checkEmailExists($conn, $email) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }
    return false;
}

function insertUser($conn, $name, $email, $password_hash, $role) {
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password_hash, $role);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function getAllUsers($conn) {
    $sql = "SELECT id, name, email, role FROM users ORDER BY id DESC";
    return mysqli_query($conn, $sql);
}

function deleteUserById($conn, $id) {
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function searchUsers($conn, $query) {
    $search = "%" . $query . "%";
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, role FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC");
    $users = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $search, $search);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    return $users;
}
?>
