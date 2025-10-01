<?php
// Nạp file UserDAO và khởi tạo session
require_once __DIR__ . '/../DAO/userDAO.php';
// --- BEGIN STANDARDIZED ADMIN AUTH CHECK ---
$userDAO = new UserDAO(); 
$user = null; 
if (isset($_SESSION['user_id'])) {
    $user = $userDAO->findById($_SESSION['user_id']);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /VINICA/login");
    exit;
} elseif (!$user || $user['role'] !== 'admin') { 
    header("Location: /VINICA/login");
    exit;
}
// --- END STANDARDIZED ADMIN AUTH CHECK ---

// Lấy user ID từ URL
$user_id_to_delete = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($user_id_to_delete) {
    // Ngăn chặn admin tự xóa tài khoản của chính mình
    if (isset($_SESSION['user_id']) && $user_id_to_delete == $_SESSION['user_id']) {
        $_SESSION['user_message'] = 'Error: You cannot delete your own account.';
        $_SESSION['user_message_type'] = 'danger';
    } else {
        try {
            if ($userDAO->deleteUserById($user_id_to_delete)) {
                $_SESSION['user_message'] = 'User deleted successfully.';
                $_SESSION['user_message_type'] = 'success';
            } else {
                // Trường hợp deleteUserById trả về false thay vì throw exception (hiện tại DAO đang là void hoặc throw)
                $_SESSION['user_message'] = 'Failed to delete user. The user might not exist or another error occurred.';
                $_SESSION['user_message_type'] = 'danger';
            }
        } catch (PDOException $e) {
            // Ghi log lỗi
            // error_log("PDOException while deleting user: " . $e->getMessage());
            $_SESSION['user_message'] = 'Database error while deleting user. Please try again.';
            $_SESSION['user_message_type'] = 'danger';
        } catch (Exception $e) {
            // Bắt các exception khác, ví dụ từ việc cố gắng tự xóa (nếu DAO có throw)
            $_SESSION['user_message'] = 'Error: ' . $e->getMessage();
            $_SESSION['user_message_type'] = 'danger';
        }
    }
} else {
    $_SESSION['user_message'] = 'Invalid user ID for deletion.';
    $_SESSION['user_message_type'] = 'warning';
}

header("Location: /VINICA/admin-dashboard/user-management");
exit;
?> 