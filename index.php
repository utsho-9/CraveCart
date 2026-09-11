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

    case 'createUser':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");
        require_once 'controllers/AdminController.php';
        (new AdminController())->createUser();
        break;

    case 'deleteUser':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");
        require_once 'controllers/AdminController.php';
        (new AdminController())->deleteUser();
        break;

    case 'searchUsers':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");
        require_once 'controllers/AdminController.php';
        (new AdminController())->searchUsers();
        break;

    case 'approvePayment':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");
        require_once 'controllers/AdminController.php';
        (new AdminController())->approvePayment();
        break;

    case 'moderateFeedback':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");
        require_once 'controllers/AdminController.php';
        (new AdminController())->moderateFeedback();
        break;

    case 'createProduct':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant') die("Unauthorized");
        require_once 'controllers/RestaurantController.php';
        (new RestaurantController())->createProduct();
        break;

    case 'deleteProduct':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant') die("Unauthorized");
        require_once 'controllers/RestaurantController.php';
        (new RestaurantController())->deleteProduct();
        break;

    case 'searchProducts':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant') die("Unauthorized");
        require_once 'controllers/RestaurantController.php';
        (new RestaurantController())->searchProducts();
        break;

    case 'toggleAvailability':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant') die("Unauthorized");
        require_once 'controllers/RestaurantController.php';
        (new RestaurantController())->toggleAvailability();
        break;

    case 'markPreparing':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant') die("Unauthorized");
        require_once 'controllers/RestaurantController.php';
        (new RestaurantController())->markPreparing();
        break;

    case 'placeOrder':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') die("Unauthorized");
        require_once 'controllers/CustomerController.php';
        (new CustomerController())->placeOrder();
        break;

    case 'updateRequest':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') die("Unauthorized");
        require_once 'controllers/CustomerController.php';
        (new CustomerController())->updateRequest();
        break;

    case 'cancelOrder':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') die("Unauthorized");
        require_once 'controllers/CustomerController.php';
        (new CustomerController())->cancelOrder();
        break;

    case 'searchMenu':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') die("Unauthorized");
        require_once 'controllers/CustomerController.php';
        (new CustomerController())->searchMenu();
        break;

    case 'submitFeedback':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') die("Unauthorized");
        require_once 'controllers/CustomerController.php';
        (new CustomerController())->submitFeedback();
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