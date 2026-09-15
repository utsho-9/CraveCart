<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_destroy();
setcookie("user_login", "", time() - 3600, "/");
header("Location: index.php");
exit();
?>
