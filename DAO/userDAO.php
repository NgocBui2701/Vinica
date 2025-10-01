<?php
require_once 'pdo.php';

class UserDAO {
    public function getAllUsers() {
        return pdo_query("SELECT * FROM users");
    }

    public function findByUsernameOrEmail($username, $email) {
        return pdo_query_one(
            "SELECT * FROM users WHERE username = ? OR email = ?",
            $username,
            $email
        );
    }

    public function findById($user_id) {
        return pdo_query_one(
            "SELECT * FROM users WHERE id = ?",
            $user_id
        );
    }
    
    public function create($username, $email, $password_hash, $verification_token, $role) {
        pdo_execute(
            "INSERT INTO users (username, email, password, verification_token, role) VALUES (?, ?, ?, ?, ?)",
            $username,
            $email,
            $password_hash,
            $verification_token,
            $role
        );
        return true;
    }

    public function updatePassword($user_id, $password_hash) {
        pdo_execute(
            "UPDATE users SET password = ? WHERE id = ?",
            $password_hash,
            $user_id
        );
    }

    public function updateEmail($user_id, $email, $email_verified) {
        pdo_execute(
            "UPDATE users SET email = ?, email_verified = ? WHERE id = ?",
            $email,
            $email_verified,
            $user_id
        );
    }

    public function deleteUserById($user_id) {
        $sql = "DELETE FROM users WHERE id = ?";
        pdo_execute($sql, $user_id);
        return true;
    }

    public function updateUser($user_id, $username, $email, $role) {
        $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        pdo_execute($sql, $username, $email, $role, $user_id);
        return true;
    }
    public function updateUserVerificationDetails(string $user_id, string $token, bool $isVerified) {
        $sql = "UPDATE users SET verification_token = ?, email_verified = ? WHERE id = ?";
        pdo_execute($sql, $token, (int)$isVerified, $user_id);
        return true; // Assuming success if no exception is thrown
    }
}
?>