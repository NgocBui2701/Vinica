<?php
require_once __DIR__ . '/../DAO/userDAO.php';
require_once __DIR__ . '/../DAO/mailer.php';

// Tạo token CSRF nếu chưa tồn tại trong session
// Token CSRF giúp bảo vệ chống lại tấn công Cross-Site Request Forgery.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Kiểm tra xác thực và phân quyền người dùng
// Chỉ admin mới có quyền truy cập trang này.
$userDAO = new UserDAO(); // Khởi tạo UserDAO sớm hơn để sử dụng cho kiểm tra bên dưới
$userForAuth = null; // Khởi tạo biến user
if (isset($_SESSION['user_id'])) {
    $userForAuth = $userDAO->findById($_SESSION['user_id']);
}

if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, chuyển hướng về trang login
    header("Location: /VINICA/login");
    exit;
} elseif (!$userForAuth || $userForAuth['role'] !== 'admin') { 
    header("Location: /VINICA/login"); // Chuyển hướng về login nếu không phải admin
    exit;
}

// Khởi tạo các biến cho form và mảng lỗi
$username = '';
$email = '';
$role = 'staff'; // Vai trò mặc định khi thêm người dùng mới
$errors = []; // Mảng lưu trữ các lỗi validation

// Xử lý khi form được submit (phương thức POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token. Please try again.';
    } else {
        // Lấy dữ liệu từ form và làm sạch (trim)
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'staff';

        // Validate dữ liệu
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number.";
        } elseif (!preg_match('/[\W_]/', $password)) { // \W là bất kỳ ký tự không phải từ
            $errors[] = "Password must contain at least one special character.";
        }
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }
        if (strlen($username) < 3 || strlen($username) > 20) {
            $errors[] = "Username length is not valid.";
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = "Username can only contain letters, numbers, and underscores.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif (strlen($email) > 100) { // kích thước tối đa của email
            $errors[] = "Email is too long.";
        }
        if (strlen($password) > 60) { // kích thước tối đa của mật khẩu (giới hạn bcrypt là 72 bytes, nhưng 60 là hợp lý)
            $errors[] = "Password is too long.";
        }
        if (!in_array($role, ['admin', 'staff'])) {
            $errors[] = 'Invalid role selected.';
        }

        // Kiểm tra username hoặc email đã tồn tại chưa (nếu không có lỗi validate cơ bản)
        if (empty($errors)) { 
            $existingUser = $userDAO->findByUsernameOrEmail($username, $email);
            if ($existingUser) {
                if ($existingUser['username'] === $username) {
                    $errors[] = 'Username already taken.'; 
                }
                if ($existingUser['email'] === $email) {
                    $errors[] = 'Email already registered.'; 
                }
            }
        }

        // Nếu không có lỗi nào, tiến hành tạo người dùng
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Use BCRYPT as in register.php
            $verification_token = bin2hex(random_bytes(32)); // Token for email verification

            // Gửi email xác thực trước khi tạo người dùng
            if (class_exists('Mailer') && method_exists('Mailer', 'sendVerificationEmail')) {
                if (Mailer::sendVerificationEmail($email, $verification_token)) {
                    // Email đã được gửi thành công, bây giờ tạo người dùng
                    try {
                        // Giả sử UserDAO->create bây giờ lưu trữ vai trò
                        if ($userDAO->create($username, $email, $hashedPassword, $verification_token, $role)) {
                            // Note: User is created with email_verified = 0 (or default) by the DB.
                            // The verification token is stored. User needs to verify.
                            $_SESSION['user_message'] = 'User added successfully. A verification email has been sent to ' . htmlspecialchars($email) . '.';
                            $_SESSION['user_message_type'] = 'success';
                            unset($_SESSION['csrf_token']);
                            header("Location: /VINICA/admin-dashboard/user-management");
                            exit;
                        } else {
                            $errors[] = 'Failed to create user in database after sending email.';
                        }
                    } catch (PDOException $e) {
                        $errors[] = 'Database error while creating user: ' . $e->getMessage();
                    }
                } else {
                    $errors[] = 'Failed to send verification email to ' . htmlspecialchars($email) . '. User not created.';
                }
            } else {
                 $errors[] = 'Mailer class or sendVerificationEmail method not found. Cannot send verification email.';
            }
        }
    }
    if(!empty($errors)){
        unset($_SESSION['csrf_token']); 
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Thiết lập các biến cho thẻ <title>, <meta description>, <meta keywords> của trang HTML
$title = "Add New User | VINICA Admin"; 
$description = "Add a new admin or staff account for VINICA."; 
$keywords = "VINICA, admin, add user, new account"; 

ob_start();
?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard/user-management">User Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New User</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="mb-0">Add New User</h1> 
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="/VINICA/admin-dashboard/user-management/user-add" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label> 
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label> 
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label> 
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label> 
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label> 
                            <select class="form-select" id="role" name="role">
                                <option value="staff" <?php echo ($role === 'staff') ? 'selected' : ''; ?>>Staff</option> 
                                <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Admin</option> 
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="/VINICA/admin-dashboard/user-management" class="btn btn-secondary me-2">Cancel</a> 
                            <button type="submit" class="btn btn-primary">Add User</button> 
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