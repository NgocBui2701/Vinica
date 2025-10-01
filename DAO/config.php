<?php
session_set_cookie_params([
    'lifetime' => 1800, // 30 minutes
    'path' => '/VINICA/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
if (isset($_SESSION['user_id']) && !isset($_SESSION['session_regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = true;
}

require_once 'pdo.php';
require_once __DIR__ . '/../vendor/autoload.php';

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'ngocbui27012109@gmail.com');
define('SMTP_PASS', 'mwgk gvla raav fqcc');
define('SMTP_PORT', 587);

?>