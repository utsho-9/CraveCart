<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/DatabaseConnection.php';
require_once __DIR__ . '/../Model/userModel.php';

function handleRegistration() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';

        $allowed_roles = ['customer', 'restaurant', 'delivery'];
        if (!in_array($role, $allowed_roles)) {
            $error = "Invalid account role selected!";
            include __DIR__ . '/../View/registration.php';
            return;
        }

        if (empty($name) || empty($email) || empty($password)) {
            $error = "All fields are required!";
            include __DIR__ . '/../View/registration.php';
            return;
        }

        $conn = openConnection();

        if (checkEmailExists($conn, $email)) {
            $error = "Email is already registered!";
            include __DIR__ . '/../View/registration.php';
            return;
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        if (insertUser($conn, $name, $email, $hashed_password, $role)) {
            header("Location: index.php?action=login&success=true");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
            include __DIR__ . '/../View/registration.php';
            return;
        }
    } else {
        include __DIR__ . '/../View/registration.php';
    }
}

handleRegistration();
?>
