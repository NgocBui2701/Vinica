<?php
require_once __DIR__ . '/pdo.php';

class ServiceDAO {

    /**
     * Lấy tất cả các dịch vụ được phép hiển thị, sắp xếp theo thứ tự.
     * @return array Danh sách các dịch vụ.
     */
    public function getAllVisibleServices() {
        $sql = "SELECT * FROM services WHERE is_visible = ? ORDER BY display_order ASC, name ASC";
        return pdo_query($sql, true);
    }

    /**
     * Lấy tất cả các địa điểm/không gian (venues) được phép hiển thị cho một service_id cụ thể, sắp xếp theo thứ tự.
     * @param int $serviceId ID của dịch vụ cha.
     * @return array Danh sách các venues.
     */
    public function getVisibleVenuesByServiceId($serviceId) {
        $sql = "SELECT * FROM venues WHERE service_id = ? AND is_visible = ? ORDER BY display_order ASC, name ASC";
        return pdo_query($sql, $serviceId, true);
    }
    
    // ----- CÁC PHƯƠNG THỨC CHO TRANG ADMIN (Sẽ thêm sau) -----

    /**
     * Lấy tất cả dịch vụ (kể cả ẩn) cho trang quản lý.
     */
    public function getAllServicesForAdmin() {
        $sql = "SELECT * FROM services ORDER BY display_order ASC, name ASC";
        return pdo_query($sql);
    }

    /**
     * Lấy một dịch vụ bằng ID.
     */
    public function getServiceById($id) {
        $sql = "SELECT * FROM services WHERE id = ?";
        return pdo_query_one($sql, $id);
    }
    
    /**
     * Lấy tất cả các venue (kể cả ẩn) của một service_id cho trang admin
     */
    public function getAllVenuesByServiceIdForAdmin($serviceId) {
        $sql = "SELECT * FROM venues WHERE service_id = ? ORDER BY display_order ASC, name ASC";
        return pdo_query($sql, $serviceId);
    }

    /**
     * Lấy một venue bằng ID.
     */
    public function getVenueById($id) {
        $sql = "SELECT * FROM venues WHERE id = ?";
        return pdo_query_one($sql, $id);
    }

    /**
     * Tạo mới một dịch vụ.
     * @param array $data Mảng chứa thông tin dịch vụ.
     *                    Keys: name, slug, description, image_url, is_visible, display_order
     * @return bool True on success, false on failure.
     */
    public function createService($data): bool {
        $sql = "INSERT INTO services (name, slug, description, image_url, is_visible, display_order) 
                VALUES (?, ?, ?, ?, ?, ?)";
        try {
            pdo_execute(
                $sql, 
                $data['name'], 
                $data['slug'], 
                $data['description'], 
                $data['image_url'], 
                $data['is_visible'], 
                $data['display_order']
            );
            return true; // Explicitly return true on successful execution
        } catch (Exception $e) {
            // Log error $e->getMessage(); (optional)
            return false; // Return false if an exception occurred
        }
    }

    /**
     * Cập nhật một dịch vụ.
     * @param int $id ID của dịch vụ cần cập nhật.
     * @param array $data Mảng chứa thông tin dịch vụ.
     *                    Keys: name, slug, description, image_url, is_visible, display_order
     * @return bool True on success, false on failure.
     */
    public function updateService($id, $data): bool {
        $sql = "UPDATE services SET 
                name = ?, 
                slug = ?, 
                description = ?, 
                image_url = ?, 
                is_visible = ?, 
                display_order = ?
                WHERE id = ?";
        try {
            pdo_execute(
                $sql, 
                $data['name'], 
                $data['slug'], 
                $data['description'], 
                $data['image_url'], 
                $data['is_visible'], 
                $data['display_order'], 
                $id
            );
            return true;
        } catch (Exception $e) {
            // Log error $e->getMessage(); (optional)
            return false;
        }
    }

    /**
     * Xóa một dịch vụ.
     * @param int $id ID của dịch vụ cần xóa.
     * @return bool True on success, false on failure.
     */
    public function deleteService($id): bool {
        $sql = "DELETE FROM services WHERE id = ?";
        // ON DELETE CASCADE trong DB sẽ tự động xóa các venues liên quan.
        try {
            pdo_execute($sql, $id);
            return true;
        } catch (Exception $e) {
            // Log error $e->getMessage(); (optional)
            return false;
        }
    }

    /**
     * Tạo mới một venue.
     */
    public function createVenue($serviceId, $name, $capacity, $description, $imageUrl, $isVisible, $displayOrder): bool {
        $sql = "INSERT INTO venues (service_id, name, capacity, description, image_url, is_visible, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        try {
            pdo_execute($sql, $serviceId, $name, $capacity, $description, $imageUrl, $isVisible, $displayOrder);
            return true;
        } catch (Exception $e) {
            // Log error $e->getMessage(); (optional)
            return false;
        }
    }

    /**
     * Cập nhật một venue.
     */
    public function updateVenue($id, $data): bool {
        $sql = "UPDATE venues SET 
                service_id = ?, 
                name = ?, 
                capacity = ?, 
                description = ?, 
                image_url = ?, 
                is_visible = ?, 
                display_order = ? 
                WHERE id = ?";
        try {
            pdo_execute(
                $sql, 
                $data['service_id'], 
                $data['name'], 
                $data['capacity'], 
                $data['description'], 
                $data['image_url'], 
                $data['is_visible'], 
                $data['display_order'], 
                $id
            );
            return true;
        } catch (Exception $e) {
            // Log error $e->getMessage(); (optional)
            return false;
        }
    }

    /**
     * Xóa một venue.
     */
    public function deleteVenue($id): bool {
        $sql = "DELETE FROM venues WHERE id = ?";
        try {
            pdo_execute($sql, $id);
            return true;
        } catch (Exception $e) {
            // Log error $e->getMessage(); (optional)
            return false;
        }
    }

    /**
     * Lấy một dịch vụ bằng slug của nó (chỉ các dịch vụ visible).
     */
    public function getVisibleServiceBySlug($slug) {
        $sql = "SELECT * FROM services WHERE slug = ? AND is_visible = ?";
        return pdo_query_one($sql, $slug, true);
    }

}
?> 