<?php 
require_once 'pdo.php';
function insert_reservation($data) {
    $sql = "INSERT INTO reservations (full_name, phone, email, service, check_in_date, area, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    try {
        $status = isset($data['status']) ? $data['status'] : 'pending';
        pdo_execute(
            $sql, 
            $data['full_name'], 
            $data['phone'], 
            $data['email'], 
            $data['service'], 
            $data['check_in_date'], 
            $data['area'], 
            $status
        );
        return true;
    } catch (PDOException $e) {
        error_log("Error inserting reservation: " . $e->getMessage());
        return false;
    }
}
function get_reservations() {
    $sql = "SELECT * FROM reservations ORDER BY created_at DESC";
    return pdo_query($sql);
}
?>