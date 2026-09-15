<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = isset($_GET['action']) ? $_GET['action'] : 'login';

switch ($action) {

    case 'login':
        require_once 'Controller/loginValidation.php';
        break;

    case 'register':
        require_once 'Controller/registrationValidation.php';
        break;

    case 'logout':
        require_once 'Controller/logout.php';
        break;

    case 'restaurant_dash':
        require_once 'Controller/restaurantController.php';
        restaurantIndex();
        break;

    case 'customer_dash':
        require_once 'Controller/customerController.php';
        customerIndex();
        break;

    case 'delivery_dash':
        require_once 'Controller/deliveryController.php';
        deliveryIndex();
        break;

    case 'admin_dash':
        require_once 'Controller/adminController.php';
        adminIndex();
        break;

    case 'createUser':
        require_once 'Controller/adminController.php';
        adminCreateUser();
        break;

    case 'deleteUser':
        require_once 'Controller/adminController.php';
        adminDeleteUser();
        break;

    case 'searchUsers':
        require_once 'Controller/adminController.php';
        adminSearchUsers();
        break;

    case 'approvePayment':
        require_once 'Controller/adminController.php';
        adminApprovePayment();
        break;

    case 'moderateFeedback':
        require_once 'Controller/adminController.php';
        adminModerateFeedback();
        break;

    case 'createProduct':
        require_once 'Controller/restaurantController.php';
        restaurantCreateProduct();
        break;

    case 'deleteProduct':
        require_once 'Controller/restaurantController.php';
        restaurantDeleteProduct();
        break;

    case 'searchProducts':
        require_once 'Controller/restaurantController.php';
        restaurantSearchProducts();
        break;

    case 'toggleAvailability':
        require_once 'Controller/restaurantController.php';
        restaurantToggleAvailability();
        break;

    case 'markPreparing':
        require_once 'Controller/restaurantController.php';
        restaurantMarkPreparing();
        break;

    case 'placeOrder':
        require_once 'Controller/customerController.php';
        customerPlaceOrder();
        break;

    case 'updateRequest':
        require_once 'Controller/customerController.php';
        customerUpdateRequest();
        break;

    case 'cancelOrder':
        require_once 'Controller/customerController.php';
        customerCancelOrder();
        break;

    case 'searchMenu':
        require_once 'Controller/customerController.php';
        customerSearchMenu();
        break;

    case 'submitFeedback':
        require_once 'Controller/customerController.php';
        customerSubmitFeedback();
        break;

    case 'addNote':
        require_once 'Controller/deliveryController.php';
        deliveryAddNote();
        break;

    case 'deleteNote':
        require_once 'Controller/deliveryController.php';
        deliveryDeleteNote();
        break;

    case 'searchByZone':
        require_once 'Controller/deliveryController.php';
        deliverySearchByZone();
        break;

    case 'updateDeliveryStatus':
        require_once 'Controller/deliveryController.php';
        deliveryUpdateStatus();
        break;

    case 'collectCash':
        require_once 'Controller/deliveryController.php';
        deliveryCollectCash();
        break;

    default:
        echo "<h1>404 - Page Not Found</h1>";
        break;
}
?>