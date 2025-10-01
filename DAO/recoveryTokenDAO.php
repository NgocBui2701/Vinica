<?php
require_once 'pdo.php';

class RecoveryTokenDAO {
    public function create($user_id, $token, $expires_at) {
        pdo_execute(
            "INSERT INTO recovery_tokens (user_id, token, expires_at) VALUES (?, ?, ?)",
            $user_id,
            $token,
            $expires_at
        );
    }
    public function update($token) {
        pdo_execute(
            "UPDATE recovery_tokens SET used = 1 WHERE token = ?",
            $token
        );
    }
    public function deleteOldTokens($user_id) {
        pdo_execute(
            "DELETE FROM recovery_tokens WHERE user_id = ?",
            $user_id
        );
    }
    public function findByToken($token) {
        return pdo_query_one(
            "SELECT * FROM recovery_tokens WHERE token = ? AND used = 0 AND expires_at > NOW()",
            $token
        );
    }
}
?>