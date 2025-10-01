<?php
require_once __DIR__ . '/../../DAO/UserDAO.php';
require_once __DIR__ . '/../../DAO/ServiceDAO.php';
require_once __DIR__ . '/../../DAO/pdo.php';

// Kiểm tra xác thực và quyền admin
$userDAO = new UserDAO();
$loggedInUser = null;
if (isset($_SESSION['user_id'])) {
    $loggedInUser = $userDAO->findById($_SESSION['user_id']);
}

if (!$loggedInUser) {
    $_SESSION['error_message'] = "Authentication required.";
    header("Location: /VINICA/login");
    exit;
} elseif ($loggedInUser['role'] !== 'admin') {
    $_SESSION['error_message'] = "Admin access required.";
    header("Location: /VINICA/admin-dashboard");
    exit;
}

$serviceDAO = new ServiceDAO();

// $action_type được truyền từ $routes trong index.php thông qua extract($params)
if (!isset($action_type)) {
    $_SESSION['error_message'] = "No action specified for venue.";
    // Cố gắng redirect về trang service chung nếu không có service_id cụ thể
    header("Location: /VINICA/admin-dashboard/service-management");
    exit;
}

$redirect_service_id = null;
if (isset($_POST['service_id_redirect']) && filter_var($_POST['service_id_redirect'], FILTER_VALIDATE_INT)) {
    $redirect_service_id = (int)$_POST['service_id_redirect'];
} elseif (isset($_GET['service_id_redirect']) && filter_var($_GET['service_id_redirect'], FILTER_VALIDATE_INT)) {
    $redirect_service_id = (int)$_GET['service_id_redirect'];
}

$base_redirect_url = "/VINICA/admin-dashboard/service-management";
if ($redirect_service_id) {
    $base_redirect_url = "/VINICA/admin-dashboard/service-management/venues?service_id=" . $redirect_service_id;
}

if ($action_type === 'delete_venue') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['venue_id']) || !filter_var($_POST['venue_id'], FILTER_VALIDATE_INT)) {
            $_SESSION['error_message'] = "Invalid Venue ID for deletion.";
        } else {
            $venue_id_to_delete = (int)$_POST['venue_id'];
            $venue_to_delete = $serviceDAO->getVenueById($venue_id_to_delete);
            $venue_name_deleted = $venue_to_delete ? $venue_to_delete['name'] : 'ID ' . $venue_id_to_delete;

            if ($serviceDAO->deleteVenue($venue_id_to_delete)) {
                $_SESSION['success_message'] = "Venue '" . htmlspecialchars($venue_name_deleted) . "' has been deleted successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to delete venue '" . htmlspecialchars($venue_name_deleted) . "'. Database error or venue not found.";
            }
        }
    } else {
        $_SESSION['error_message'] = "Invalid request method for deleting venue.";
    }
    header("Location: " . $base_redirect_url);
    exit;
}

$_SESSION['error_message'] = "Unknown action for venue: " . htmlspecialchars($action_type);
header("Location: " . $base_redirect_url);
exit;
?> 