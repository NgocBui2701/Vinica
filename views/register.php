<?php
require_once __DIR__ . '/../DAO/userDAO.php';
require_once __DIR__ . '/../DAO/mailer.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    // Get the password directly from POST (not sanitized, as it will be hashed later)
    $password = $_POST['password'];
    // Validate input data
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
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = "Username must be between 3 and 20 characters long.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username can only contain letters, numbers, and underscores.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($email) > 100) {
        $error = "Email is too long.";
    } elseif (strlen($password) > 60) {
        $error = "Password is too long.";
    } elseif (strlen($username) > 20) {
        $error = "Username is too long.";
    // All validations passed, proceed with registration
    } else {
        // Instantiate UserDAO and check if the username or email already exists
        $userDAO = new UserDAO();
        $existing = $userDAO->findByUsernameOrEmail($username, $email);
        if ($existing) {
            $error = "Username or email already exists.";
        } else {
            // Hash the password and generate a token for email verification
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $token = bin2hex(random_bytes(32));
            // Send verification email
            // Assuming Mailer::sendVerificationEmail is a method that sends the email
            if (Mailer::sendVerificationEmail($email, $token)) {
                // If email sent successfully, create the user in the database
                $userDAO->create($username, $email, $password_hash, $token);
                $success = "Successfully registered. Please check your email to verify your account.";
            } else {
                $error = "Failed to send verification email.";
            }
        }
    }
}

$title = "Sign Up";
ob_start();
?>
<div class="login-form-wrapper form-box">
    <h2>Sign Up</h2>
    <form method="POST">
        <div class="input-box">
            <input type="text" name="username" required>
            <label for="username">Username</label>
        </div>
        <div class="input-box">
            <input type="email" name="email" required>
            <label for="email">Email</label>
        </div>
        <div class="input-box">
            <input type="password" name="password" required>
            <label for="password">Password</label>
            <i class='bx bx-low-vision show-password'></i>
        </div>
        <div class="input-box">
            <input type="password" name="confirm_password" required>
            <label for="confirm_password">Confirm Password</label>
            <i class='bx bx-low-vision show-password'></i>
        </div>
        <button type="submit" class="btn btn-custom mx-auto"><h4>Sign Up</h4></button>
            <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
            <?php if (isset($success)) echo "<p class='success'>" . htmlspecialchars($success) . "</p>"; ?>
        <p>Already have an account? <a href="/VINICA/login">Login</a></p>
    </form>
</div>
<?php
$content = ob_get_clean();
require 'main.php';
?>