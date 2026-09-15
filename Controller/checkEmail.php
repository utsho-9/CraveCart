<?php
require_once __DIR__ . '/../Model/DatabaseConnection.php';
require_once __DIR__ . '/../Model/userModel.php';

header('Content-Type: application/json');

$email = trim($_POST['email'] ?? $_GET['email'] ?? '');

if (!$email) {
    echo json_encode(["status" => "empty", "message" => "Email is required"]);
} else {
    $conn = openConnection();
    if (checkEmailExists($conn, $email)) {
        echo json_encode(["status" => "taken", "message" => "Email is already taken"]);
    } else {
        echo json_encode(["status" => "available", "message" => "Email is available"]);
    }
}
?>
