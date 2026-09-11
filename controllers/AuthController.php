<?php
require_once 'models/Database.php';
require_once 'models/User.php';

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $db = (new Database())->getConnection();
            $userModel = new User($db);
            $user = $userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];

                if (isset($_POST['remember'])) {
                    setcookie("user_login", $user['id'], time() + (86400 * 7), "/");
                }

                header("Location: index.php?action=" . $user['role'] . "_dash");
                exit;
            } else {
                $error = "Invalid Email or Password!";
                include 'views/login.php';
            }
        } else {
            include 'views/login.php';
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];

            $allowed_roles = ['customer', 'restaurant', 'delivery'];
            if (!in_array($role, $allowed_roles)) {
                die("Invalid role selected!");
            }

            $db = (new Database())->getConnection();

            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Email is already registered!";
                include 'views/register.php';
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $insert = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
                $insert->bind_param("ssss", $name, $email, $hashed_password, $role);

                if ($insert->execute()) {
                    header("Location: index.php?action=login&success=true");
                    exit;
                } else {
                    $error = "Registration failed. Please try again.";
                    include 'views/register.php';
                }
            }
        } else {
            include 'views/register.php';
        }
    }

    public function logout() {
        session_destroy();
        setcookie("user_login", "", time() - 3600, "/");
        header("Location: index.php");
        exit;
    }
}
?>
