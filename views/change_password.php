<?php
require_once __DIR__ . '/../DAO/userDAO.php';

// Check if the user is logged in and has a valid session
if (!isset($_SESSION['user_id'])) {
    header("Location: /VINICA/login");
    exit;
}
$userDAO = new UserDAO();
$user = $userDAO->findById($_SESSION['user_id']);
if (!$user) {
    $error = "User not found.";
    unset($_SESSION['user_id']);
    header("Location: /VINICA/login");
    exit;
}
// Handle POST request for changing password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize the input fields
    $current_password = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_STRING);
    $new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
    // Validate the input fields
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $error = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        $error = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $error = "Password must contain at least one number.";
    } elseif (!preg_match('/[\W_]/', $new_password)) {
        $error = "Password must contain at least one special character.";
    } elseif ($new_password === $current_password) {
        $error = "New password cannot be the same as current password.";
    } elseif (strlen($new_password) > 60) {
        $error = "New password is too long.";
    } else {
        // Update the password in the database
        // Hash the new password using password_hash
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $userDAO->updatePassword($_SESSION['user_id'], $password_hash);
        $success = "Password changed successfully.";
    }
}

$title = "Change Password";
ob_start();
?>
<div class="login-form-wrapper form-box my-5">
    <h2>Change Password</h2>
    <form method="POST">
        <div class="input-box">
            <input type="password" name="current_password" required>
            <label for="current_password">Current Password</label>
            <i class='bx bx-low-vision show-password'></i>
        </div>
        <div class="input-box">
            <input type="password" name="new_password" required>
            <label for="new_password">New Password</label>
            <i class='bx bx-low-vision show-password'></i>
        </div>
        <div class="input-box">
            <input type="password" name="confirm_password" required>
            <label for="confirm_password">Confirm New Password</label>
            <i class='bx bx-low-vision show-password'></i>
        </div>
        <button type="submit" class="btn btn-custom"><h4>Change Password</h4></button>
            <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
            <?php if (isset($success)) echo "<p class='success'>" . htmlspecialchars($success) . "</p>"; ?>
    </form>
    <div class="logreg-link">
        <p><a href="/VINICA/dashboard">Back to Dashboard</a></p>
    </div>
</div>
<?php
    $content = ob_get_clean();
    require 'main.php';
?>