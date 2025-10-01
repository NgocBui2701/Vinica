<?php
require_once __DIR__ . '/../DAO/recoveryTokenDAO.php';
require_once __DIR__ . '/../DAO/userDAO.php';

// Handle password reset with token
if (!isset($_GET['token'])) {
    $error = "Token is invalid.";
} else {
    // Get the token from the URL
    $token = $_GET['token'];
    // Instantiate RecoveryTokenDAO and find token data
    $tokenDAO = new RecoveryTokenDAO();
    $tokenData = $tokenDAO->findByToken($token);
    // Check if the token is valid and not expired
    if (!$tokenData) {
        $error = "Token is invalid, already used, or expired.";
    } else {
        // Handle POST request for password reset
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the new password and confirm password from POST
            $password = $_POST['password'];
            // Validate the password
            if (strlen($password) < 8) {
                $error = "Password must be at least 8 characters long.";
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $error = "Password must contain at least one uppercase letter.";
            } elseif (!preg_match('/[a-z]/', $password)) {
                $error = "Password must contain at least one lowercase letter.";
            } elseif (!preg_match('/[0-9]/', $password)) {
                $error = "Password must contain at least one number.";
            } elseif (!preg_match('/[\W_]/', $password)) {
                $error = "Password must contain at least one special character.";
            } elseif ($password !== $_POST['confirm_password']) {
                $error = "Passwords do not match.";
            } elseif (strlen($password) > 60) {
                $error = "Password is too long.";
            // All validations passed, proceed to update the password
            } else {
                // Hash the new password using password_hash
                // Use PASSWORD_BCRYPT for better security
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                // Update the user's password in the database
                $userDAO = new UserDAO();
                $userDAO->updatePassword($tokenData['user_id'], $password_hash);
                // Update the token status to used
                $recoveryTokenDAO = new RecoveryTokenDAO();
                $recoveryTokenDAO->update($token);
                $success = "Password reset successfully. You can now log in.";
            }
        }
    }
}

$title = "Reset Password";
ob_start();
?>
<div class="login-form-wrapper form-box my-5">
<h2>Reset Password</h2>
    <?php if (isset($success)) { ?>
        <a href="/VINICA/login" class="btn btn-custom-outline"><h4>Back to Login</h4></a>
    <?php } else { ?>
        <form method="POST">
            <div class="input-box">
                <input type="password" name="password" required>
                <label for="password">New Password</label>
                <i class='bx bx-low-vision show-password'></i>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" required>
                <label for="confirm_password">Confirm Password</label>
                <i class='bx bx-low-vision show-password'></i>
            </div>
            <button type="submit" class="btn btn-custom"><h4>Reset Password</h4></button>
            <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
            <?php if (isset($success)) echo "<p class='success'>" . htmlspecialchars($success) . "</p>"; ?>
        </form>
    <?php } ?>
</div>
<?php
$content = ob_get_clean();
require 'main.php';
?>