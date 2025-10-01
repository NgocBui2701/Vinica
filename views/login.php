<?php
require_once __DIR__ . '/../DAO/userDAO.php';
require_once __DIR__ . '/../DAO/loginAttemptDAO.php';
require_once __DIR__ . '/../DAO/config.php';
require_once __DIR__ . '/../DAO/pages.php';

$logo = get_logo();
// Tạo đối tượng LoginAttemptDAO để quản lý việc theo dõi đăng nhập
$loginAttemptDAO = new LoginAttemptDAO();
// Kiểm tra xem phương thức yêu cầu có phải là POST không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy giá trị username trực tiếp từ POST, có thể dùng trim để loại bỏ khoảng trắng thừa
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'];
    // Kiểm tra xem số lần đăng nhập gần đây có vượt quá giới hạn không
    if ($loginAttemptDAO->countRecentAttempts($username) >= 5) {
        $error = "Too many login attempts. Please try again later.";
    } else {
        // Ghi lại lần đăng nhập
        $loginAttemptDAO->logAttempt($username);
        // Tạo đối tượng UserDAO để quản lý dữ liệu người dùng
        $userDAO = new UserDAO();
        // Kiểm tra xem người dùng có tồn tại và xác thực mật khẩu không
        $user = $userDAO->findByUsernameOrEmail($username, $username);
        if ($user) {
            // Kiểm tra xem email có được xác thực không
            if (!$user['email_verified']) {
                $error = "Please verify your email first.";
            } elseif (password_verify($password, $user['password'])) {
                // Mật khẩu đúng, thiết lập biến phiên làm việc
                $_SESSION['user_id'] = $user['id'];
                // Kiểm tra vai trò người dùng và chuyển hướng tương ứng
                if (isset($user['role']) && $user['role'] === 'admin') {
                    header("Location: /VINICA/admin-dashboard");
                    exit;
                } elseif (isset($user['role']) && $user['role'] === 'staff') {
                    header("Location: /VINICA/staff");
                    exit;
                } else {
                    $error = "Invalid user role.";
                }
            } else {
                $error = "Incorrect login credentials.";
            }
        } else {
            $error = "User not found.";
        }
    }
}

$title = "LOGIN | VINICA";
ob_start();
?>
<div class="login-container container-fluid">
    <img src="<?php echo htmlspecialchars($logo['slug']); ?>" alt="Logo" class = "navbar_logo_img">

    <p class="management-title">Restaurant & Bar</p>
    <p class="management-sub-title">Management</p>

    <div class="login-form-wrapper form-box">
        <h2>Login</h2>
        <form method="POST">
            <div class="input-box">
                <input type="text" name="username" required>
                <label for="username">Username</label>
            </div>
            <div class="input-box">
                <input type="password" name="password" required>
                <label for="password">Password</label>
                <i class='bx bx-low-vision show-password'></i>   
            </div>
            <div class="options-group">
                <a href="/VINICA/recovery" class="forgot-password-link">Forgot Password ?</a>
            </div>
            <button type="submit" class="btn btn-custom"><h4>Let's Go!</h4></button>
            <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
            <?php if (isset($success)) echo "<p class='success'>" . htmlspecialchars($success) . "</p>"; ?>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require 'main.php';
?>