<?php 
    require_once 'pdo.php';

    /**
     * Lấy danh sách các item trong navbar
     * @return array Danh sách các item trong navbar
     */
    function get_navbar_items() {
        $items = pdo_query("SELECT id, name, slug, `order` FROM page_items WHERE parent_id IS NULL AND type = 'header' AND is_visible = 1 ORDER BY `order` ASC");
        $items_child = pdo_query("SELECT id, parent_id, name, slug, `order` FROM page_items WHERE type = 'header' AND is_visible = 1 ORDER BY `order` ASC");
        $nav_items = [];
        foreach ($items as $item) {
            $nav_items[$item['id']] = [
                'name' => $item['name'],
                'slug' => $item['slug'],
                'order' => $item['order'],
                'children' => []
            ];
        }
        foreach ($items_child as $item) {
            if (isset($nav_items[$item['parent_id']])) {
                $nav_items[$item['parent_id']]['children'][] = [
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'order' => $item['order']
                ];
            }
        }
        return array_values($nav_items);
    }

    function get_footer_items() {
        $items = pdo_query("SELECT id, name, slug, `order` FROM page_items WHERE parent_id IS NULL AND type = 'footer' AND is_visible = 1 AND slug IS NULL ORDER BY `order` ASC");
        $items_child = pdo_query("SELECT id, parent_id, name, slug, `order` FROM page_items WHERE type = 'footer' AND is_visible = 1 ORDER BY `order` ASC");
        $footer_items = [];
        foreach ($items as $item) {
            $footer_items[$item['id']] = [
                'name' => $item['name'],
                'slug' => $item['slug'],
                'order' => $item['order'],
                'children' => []
            ];
        }
        foreach ($items_child as $item) {
            if (isset($footer_items[$item['parent_id']])) {
                $footer_items[$item['parent_id']]['children'][] = [
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'order' => $item['order']
                ];
            }
        }
        return array_values($footer_items);
    }

    function get_footer_buttons() {
        $buttons = pdo_query("SELECT id, name, slug, `order` FROM page_items WHERE parent_id IS NULL AND type = 'footer' AND is_visible = 1 AND slug IS NOT NULL ORDER BY `order` ASC");
        return $buttons;
    }
    function get_logo() {
        $logo = pdo_query_one("SELECT slug FROM page_items WHERE type = 'logo' AND is_visible = 1");
        return $logo;
    }

    /**
     * Lấy chi tiết một trang (title, meta description, meta keywords) từ bảng `pages` bằng slug.
     * @param string $slug Slug của trang.
     * @return array|false Mảng chứa chi tiết trang hoặc false nếu không tìm thấy.
     */
    function get_page_details_by_slug(string $slug) {
        $sql = "SELECT id, slug, title, meta_description, meta_keywords FROM pages WHERE slug = ?";
        return pdo_query_one($sql, $slug);
    }

    /**
     * Cập nhật chi tiết SEO (title, meta description, meta keywords) cho một trang trong bảng `pages`.
     * @param string $slug Slug của trang cần cập nhật.
     * @param string $title Tiêu đề mới của trang.
     * @param string $metaDescription Mô tả meta mới.
     * @param string $metaKeywords Từ khóa meta mới.
     * @return bool True nếu cập nhật thành công, False nếu thất bại.
     */
    function update_page_seo_details(string $slug, string $title, string $metaDescription, string $metaKeywords): bool {
        $sql = "UPDATE pages SET title = ?, meta_description = ?, meta_keywords = ?, updated_at = CURRENT_TIMESTAMP WHERE slug = ?";
        try {
            pdo_execute($sql, $title, $metaDescription, $metaKeywords, $slug);
            return true;
        } catch (PDOException $e) {
            error_log("Error updating page SEO details: " . $e->getMessage());
            return false;
        }
    }
?>