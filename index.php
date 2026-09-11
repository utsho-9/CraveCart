<?php
session_start();

$action = isset($_GET['action']) ? $_GET['action'] : 'login';

switch ($action) {

    case 'login':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
        break;

    case 'register':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->register();
        break;

    case 'logout':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;

    case 'restaurant_dash':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant') die("Unauthorized");
        require_once 'controllers/RestaurantController.php';
        (new RestaurantController())->index();
        break;

    case 'customer_dash':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') die("Unauthorized");
        require_once 'controllers/CustomerController.php';
        (new CustomerController())->index();
        break;

    case 'delivery_dash':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delivery') die("Unauthorized");
        require_once 'controllers/DeliveryController.php';
        (new DeliveryController())->index();
        break;

    case 'admin_dash':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");
        require_once 'controllers/AdminController.php';
        (new AdminController())->index();
        break;

    case 'addNote':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delivery') die("Unauthorized");
        require_once 'controllers/DeliveryController.php';
        (new DeliveryController())->addNote();
        break;

    case 'deleteNote':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delivery') die("Unauthorized");
        require_once 'controllers/DeliveryController.php';
        (new DeliveryController())->deleteNote();
        break;

    case 'searchByZone':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delivery') die("Unauthorized");
        require_once 'controllers/DeliveryController.php';
        (new DeliveryController())->searchByZone();
        break;

    case 'updateDeliveryStatus':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delivery') die("Unauthorized");
        require_once 'controllers/DeliveryController.php';
        (new DeliveryController())->updateDeliveryStatus();
        break;

    case 'collectCash':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delivery') die("Unauthorized");
        require_once 'controllers/DeliveryController.php';
        (new DeliveryController())->collectCash();
        break;

    default:
        echo "<h1>404 - Page Not Found</h1>";
        break;
}
?>