<?php
require_once 'pdo.php';
class LoginAttemptDAO {
    public function countRecentAttempts($username) {
        return pdo_query_value(
            "SELECT COUNT(*) FROM login_attempts WHERE username = ? AND attempt_time > NOW() - INTERVAL 15 MINUTE",
            $username
        );
    }

    public function logAttempt($username) {
        pdo_execute(
            "INSERT INTO login_attempts (username) VALUES (?)",
            $username
        );
    }
}
?>