<?php
require_once __DIR__ . '/../DAO/userDAO.php';
require_once __DIR__ . '/../DAO/recoveryTokenDAO.php';
require_once __DIR__ . '/../DAO/mailer.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Handle POST request for sending password recovery email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize email input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    // Instantiate UserDAO and find user by email
    $userDAO = new UserDAO();
    $user = $userDAO->findByUsernameOrEmail($email, $email);
    // Check if the user exists
    if ($user) {
        // Generate a unique token for password recovery
        // Use bin2hex and random_bytes for better security
        $token = bin2hex(random_bytes(32));
        // Set the expiration time for the token (1 hour from now)
        // Use date() to format the expiration time
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        // Instantiate RecoveryTokenDAO to manage recovery tokens
        $recoveryTokenDAO = new RecoveryTokenDAO();
        // Delete old tokens for the user
        // Use the deleteOldTokens method to clean up expired tokens
        $recoveryTokenDAO->deleteOldTokens($user['id']);
        // Create a new recovery token in the database
        $recoveryTokenDAO->create($user['id'], $token, $expires);
        // Use Mailer::sendRecoveryEmail to send the email with the token
        if (Mailer::sendRecoveryEmail($email, $token)) {
            $success = "Link has been sent to your email for password recovery.";
        } else {
            $error = "Failed to send recovery email.";
        }
    } else {
        $error = "Email is not valid.";
    }
}

$title = "FORGET PASSWORD | VINICA";
ob_start();
?>
<div class="login-form-wrapper form-box my-5">
    <h2>Recovery Password</h2>
    <form method="POST">
        <div class="input-box">
            <input type="email" name="email" required>
            <label for="email">Email</label>
        </div>
        <button type="submit" class="btn btn-custom"><h4>Send link</h4></button>
        <a class="btn btn-custom-outline" href="/VINICA/login"><h4>Back to Login</h4></a>
        <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>" . htmlspecialchars($success) . "</p>"; ?>
    </form>
</div>
<?php
$content = ob_get_clean();
require 'main.php';
?>