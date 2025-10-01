<?php
require_once __DIR__ . '/../DAO/userDAO.php';
require_once __DIR__ . '/../DAO/mailer.php';

// --- BEGIN STANDARDIZED ADMIN AUTH CHECK ---
$userDAO = new UserDAO();
$userForAuth = null; // Đổi tên biến để không xung đột với $user_to_edit
if (isset($_SESSION['user_id'])) {
    $userForAuth = $userDAO->findById($_SESSION['user_id']);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /VINICA/login");
    exit;
} elseif (!$userForAuth || $userForAuth['role'] !== 'admin') { 
    header("Location: /VINICA/login");
    exit;
}

// Lấy ID người dùng cần chỉnh sửa từ URL
$user_id_to_edit = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$user_to_edit = null;
$errors = [];
$username = '';
$email = '';
$role = '';

if (!$user_id_to_edit) {
    $_SESSION['user_message'] = 'Invalid User ID for editing.';
    $_SESSION['user_message_type'] = 'danger';
    header("Location: /VINICA/admin-dashboard/user-management");
    exit;
}

// Lấy thông tin người dùng hiện tại để điền vào form
$user_to_edit = $userDAO->findById($user_id_to_edit);

if (!$user_to_edit) {
    $_SESSION['user_message'] = 'User not found for editing.';
    $_SESSION['user_message_type'] = 'danger';
    header("Location: /VINICA/admin-dashboard/user-management");
    exit;
}

$original_email = $user_to_edit['email']; // `Store original email for comparison`
$username = $user_to_edit['username'];
$email = $user_to_edit['email'];
$role = $user_to_edit['role'];

// Tạo token CSRF nếu chưa có (cho form edit)
if (empty($_SESSION['csrf_token_edit'])) {
    $_SESSION['csrf_token_edit'] = bin2hex(random_bytes(32));
}

// Xử lý khi form được submit (phương thức POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token_edit'] ?? '', $_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $new_email = trim($_POST['email'] ?? ''); // Dùng new_email cho rõ ràng trong quá trình xử lý
        $role = $_POST['role'] ?? '';

        // Kiểm tra hợp lệ hơn (được tham khảo từ register.php, loại bỏ password)
        if (strlen($username) < 3 || strlen($username) > 20) {
            $errors[] = "Username must be between 3 and 20 characters long.";
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = "Username can only contain letters, numbers, and underscores.";
        }
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif (strlen($new_email) > 100) {
            $errors[] = "Email is too long (max 100 characters).";
        }
        if (!in_array($role, ['admin', 'staff'])) {
            $errors[] = 'Invalid role selected.';
        }

        // Kiểm tra username hoặc email đã tồn tại (và không phải của chính user này)
        if (empty($errors)) {
            $existingUser = $userDAO->findByUsernameOrEmail($username, $new_email);
            if ($existingUser && $existingUser['id'] != $user_id_to_edit) {
                if ($existingUser['username'] === $username) {
                    $errors[] = 'Username already taken by another user.';
                }
                if ($existingUser['email'] === $new_email) {
                    $errors[] = 'Email already registered by another user.';
                }
            }
        }

        if (empty($errors)) {
            $email_changed = ($new_email !== $original_email);
            $proceed_with_db_update = true; // Đánh dấu để kiểm soát việc cập nhật DB
            $new_verification_token = null; // Khởi tạo

            if ($email_changed) {
                $new_verification_token = bin2hex(random_bytes(32));
                if (class_exists('Mailer') && method_exists('Mailer', 'sendVerificationEmail')) {
                    if (!Mailer::sendVerificationEmail($new_email, $new_verification_token)) {
                        $errors[] = 'Failed to send verification email to ' . htmlspecialchars($new_email) . '. User details not updated.';
                        $proceed_with_db_update = false; // Không cập nhật DB nếu gửi email thất bại
                    }
                } else {
                    $errors[] = 'Mailer class or sendVerificationEmail method not found. Cannot send verification email for email change.';
                    $proceed_with_db_update = false; // Không cập nhật DB nếu mailer thiếu
                }
            }

            if ($proceed_with_db_update) {
                try {
                    if ($userDAO->updateUser($user_id_to_edit, $username, $new_email, $role)) {
                        if ($email_changed) {
                            // Cập nhật token và đánh dấu email là chưa xác thực
                            $userDAO->updateUserVerificationDetails($user_id_to_edit, $new_verification_token, false);
                            $_SESSION['user_message'] = 'User updated successfully. A verification email has been sent to the new address: ' . htmlspecialchars($new_email) . '.';
                        } else {
                            $_SESSION['user_message'] = 'User updated successfully.';
                        }
                        $_SESSION['user_message_type'] = 'success';
                        unset($_SESSION['csrf_token_edit']);
                        header("Location: /VINICA/admin-dashboard/user-management");
                        exit;
                    } else {
                        $errors[] = 'Failed to update user. Please try again.';
                    }
                } catch (PDOException $e) {
                    $errors[] = 'Database error while updating user: ' . $e->getMessage();
                }
            }
        }
    }
    // Nếu có lỗi, tạo lại CSRF token mới
    if (!empty($errors)) {
        unset($_SESSION['csrf_token_edit']);
        $_SESSION['csrf_token_edit'] = bin2hex(random_bytes(32));
    }
}

// Đảm bảo sử dụng $email (là $user_to_edit['email'] ban đầu hoặc email mới trong trường hợp lỗi hiển thị lại)
// trong giá trị của form, không phải $new_email trực tiếp.
$email_to_display_in_form = $_SERVER['REQUEST_METHOD'] === 'POST' ? $new_email : $email;

$title = "Edit User | VINICA Admin";
$description = "Edit an existing admin or staff account for VINICA.";
$keywords = "VINICA, admin, edit user, update account";

ob_start();
?>
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard/user-management">User Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit User (ID: <?php echo htmlspecialchars($user_id_to_edit); ?>)</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="mb-0">Edit User: <?php echo htmlspecialchars($user_to_edit['username'] ?? 'N/A'); ?></h1>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/VINICA/admin-dashboard/user-management/user-edit?id=<?php echo htmlspecialchars($user_id_to_edit); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token_edit'] ?? ''); ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email_to_display_in_form); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role">
                                <option value="staff" <?php echo ($role === 'staff') ? 'selected' : ''; ?>>Staff</option>
                                <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        
                        <p class="form-text text-muted">
                            Password can be changed via a separate "Reset Password" functionality if needed.
                        </p>

                        <div class="d-flex justify-content-end">
                            <a href="/VINICA/admin-dashboard/user-management" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/main.php';
?> 