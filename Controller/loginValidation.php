<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/DatabaseConnection.php';
require_once __DIR__ . '/../Model/userModel.php';

function handleLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Email and Password are required!";
            include __DIR__ . '/../View/login.php';
            return;
        }

        $conn = openConnection();
        $user = getUserByEmail($conn, $email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            if (isset($_POST['remember'])) {
                setcookie("user_login", $user['id'], time() + (86400 * 7), "/");
            }

            header("Location: index.php?action=" . $user['role'] . "_dash");
            exit();
        } else {
            $error = "Invalid Email or Password!";
            include __DIR__ . '/../View/login.php';
            return;
        }
    } else {
        include __DIR__ . '/../View/login.php';
    }
}

handleLogin();
?>
