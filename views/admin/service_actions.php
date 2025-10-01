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
    // Nếu không có session, có thể đây là request không hợp lệ hoặc session đã hết hạn
    // Chuyển hướng đến trang login hoặc hiển thị lỗi
    // Đối với actions.php, thường là không có output HTML trực tiếp mà chỉ xử lý logic và redirect
    $_SESSION['error_message'] = "Authentication required.";
    header("Location: /VINICA/login");
    exit;
} elseif ($loggedInUser['role'] !== 'admin') {
    $_SESSION['error_message'] = "Admin access required.";
    header("Location: /VINICA/login");
    exit;
}

$serviceDAO = new ServiceDAO();

// Xác định action_type được truyền từ router (index.php)
// $action_type được truyền từ $routes trong index.php thông qua extract($params)
if (!isset($action_type)) {
    $_SESSION['error_message'] = "No action specified.";
    header("Location: /VINICA/admin-dashboard/service-management");
    exit;
}

if ($action_type === 'delete_service') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['service_id']) || !filter_var($_POST['service_id'], FILTER_VALIDATE_INT)) {
            $_SESSION['error_message'] = "Invalid Service ID for deletion.";
            header("Location: /VINICA/admin-dashboard/service-management");
            exit;
        }
        $service_id_to_delete = (int)$_POST['service_id'];

        // Lấy thông tin service để hiển thị tên trong thông báo (tùy chọn)
        $service_to_delete = $serviceDAO->getServiceById($service_id_to_delete);
        $service_name_deleted = $service_to_delete ? $service_to_delete['name'] : 'ID ' . $service_id_to_delete;

        if ($serviceDAO->deleteService($service_id_to_delete)) {
            $_SESSION['success_message'] = "Service '" . htmlspecialchars($service_name_deleted) . "' and its associated venues have been deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete service '" . htmlspecialchars($service_name_deleted) . "'. Database error or service not found.";
        }
    } else {
        // Nếu không phải POST, không cho phép xóa
        $_SESSION['error_message'] = "Invalid request method for deleting service.";
    }
    header("Location: /VINICA/admin-dashboard/service-management");
    exit;
}

// Nếu không có action nào khớp
$_SESSION['error_message'] = "Unknown action: " . htmlspecialchars($action_type);
header("Location: /VINICA/admin-dashboard/service-management");
exit;
?> 