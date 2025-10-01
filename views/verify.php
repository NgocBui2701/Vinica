<?php
require_once __DIR__ . '/../DAO/userDAO.php';

if (!isset($_GET['token'])) {
    $error = "Token is invalid.";
} else {
    $token = $_GET['token'];
    $userDAO = new UserDAO();
    $user = pdo_query_one("SELECT * FROM users WHERE verification_token = ?", $token);

    if (!$user) {
        $error = "Token is invalid or has already been used.";
    } else {
        pdo_execute("UPDATE users SET email_verified = 1, verification_token = NULL WHERE verification_token = ?", $token);
        $success = "Email has been verified successfully.";
    }
}

$title = "Email Verification";
ob_start();
?>
<div class="login-form-wrapper justify-content-center my-5">
    <h2>Email Verification</h2>
    <a href="/VINICA/login" class="btn btn-custom">Back to Login</a>
</div>
<?php
$content = ob_get_clean();
require 'main.php';
?>