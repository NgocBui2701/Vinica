<?php
require_once __DIR__ . '/pdo.php'; // Sử dụng các hàm tiện ích PDO

class MenuDAO {

    // Không cần constructor và $this->db nữa vì pdo.php sẽ quản lý kết nối

    /**
     * Lấy tất cả các món ăn đặc trưng (signature dishes) được đánh dấu để hiển thị trên trang chủ,
     * sắp xếp theo thứ tự hiển thị đã định.
     * @param int $limit Số lượng món ăn tối đa để lấy
     * @return array Mảng các món ăn đặc trưng
     */
    public function getSignatureDishes(int $limit = 6): array {
        // Câu SQL này đã sử dụng ? nên phù hợp với pdo_query
        $sql = "SELECT id, name, description_short as description, price_amount as price, image_url 
                FROM menu_items 
                WHERE is_featured = 1 AND is_visible = 1
                ORDER BY display_order ASC, name ASC 
                LIMIT ?";
        return pdo_query($sql, $limit);
    }

    // --- Category Methods ---

    /**
     * Lấy một danh mục menu (hiển thị) bằng slug.
     * @param string $slug Slug của danh mục
     * @return array|null Mảng thông tin danh mục hoặc null nếu không tìm thấy
     */
    public function getVisibleCategoryBySlug(string $slug): ?array {
        $sql = "SELECT id, name, slug, description FROM menu_categories WHERE slug = ? AND is_visible = 1";
        return pdo_query_one($sql, $slug);
    }

    /**
     * Lấy tất cả các danh mục menu (hiển thị), sắp xếp theo display_order.
     * @return array Mảng các danh mục
     */
    public function getAllVisibleCategories(): array {
        $sql = "SELECT id, name, slug, description FROM menu_categories WHERE is_visible = 1 ORDER BY display_order ASC";
        return pdo_query($sql);
    }

    // --- Item Methods ---

    /**
     * Lấy tất cả các món ăn/set menu (hiển thị) thuộc một category_id cụ thể, sắp xếp theo display_order.
     * @param int $categoryId ID của danh mục
     * @return array Mảng các món ăn/set menu
     */
    public function getVisibleItemsByCategoryId(int $categoryId): array {
        $sql = "SELECT id, name, slug, description_short, description_long, price_amount, price_currency, price_text_prefix, price_text_suffix, image_url, notes, tags " .
            "FROM menu_items " .
            "WHERE category_id = ? AND is_visible = 1 " .
            "ORDER BY display_order ASC";
        return pdo_query($sql, $categoryId);
    }

    /**
     * Lấy một món ăn/set menu (hiển thị) bằng slug.
     * @param string $itemSlug Slug của món ăn/set menu
     * @return array|null Mảng thông tin món ăn/set menu hoặc null nếu không tìm thấy
     */
    public function getVisibleItemBySlug(string $itemSlug): ?array {
        $sql = "SELECT mi.id, mi.name, mi.slug, mi.description_short, mi.description_long, mi.price_amount, mi.price_currency, mi.price_text_prefix, mi.price_text_suffix, mi.image_url, mi.notes, mi.tags, mc.name as category_name, mc.slug as category_slug " .
            "FROM menu_items mi " .
            "JOIN menu_categories mc ON mi.category_id = mc.id " .
            "WHERE mi.slug = ? AND mi.is_visible = 1 AND mc.is_visible = 1";
        return pdo_query_one($sql, $itemSlug);
    }

    // Các phương thức quản lý menu khác (thêm, sửa, xóa - thường dùng cho admin) có thể được thêm ở đây
    // Ví dụ:
    // public function createMenuItem($name, $categoryId, ...) { ... pdo_execute(...) ... }
    // public function updateMenuItem($id, $name, $categoryId, ...) { ... pdo_execute(...) ... }
    // public function deleteMenuItem($id) { ... pdo_execute(...) ... }
}
?> 