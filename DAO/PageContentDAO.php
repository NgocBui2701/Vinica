<?php
require_once 'pdo.php';

class PageContentDAO {

    /**
     * Lấy tất cả các mục nội dung cho một page_slug cụ thể,
     * trả về một mảng được nhóm theo section_key và sau đó theo item_order.
     * @param string $pageSlug Slug của trang.
     * @param bool $onlyVisible Nếu true, chỉ lấy các mục is_visible = 1. Mặc định là true.
     * @return array Mảng các mục nội dung, nhóm theo section_key.
     */
    public function getContentByPageSlug(string $pageSlug, bool $onlyVisible = true): array {
        $sql = "SELECT id, page_slug, section_key, item_order, content_type, content_value_text, is_visible 
                FROM page_content 
                WHERE page_slug = ?";
        
        $params = [$pageSlug];

        if ($onlyVisible) {
            $sql .= " AND is_visible = 1";
        }
        
        $sql .= " ORDER BY section_key, item_order ASC";

        $rows = pdo_query($sql, ...$params);
        
        $contentMap = [];
        if ($rows) { // Kiểm tra nếu $rows không rỗng
            foreach ($rows as $row) {
                $sectionKey = $row['section_key'];
                if (!isset($contentMap[$sectionKey])) {
                    $contentMap[$sectionKey] = [];
                }
                $contentMap[$sectionKey][] = [
                    'id' => $row['id'],
                    'type' => $row['content_type'],
                    'text' => $row['content_value_text'],
                    'order' => $row['item_order'],
                    'is_visible' => (bool)$row['is_visible'] // Chuyển đổi sang boolean
                ];
            }
        }
        return $contentMap;
    }

    /**
     * Lấy một mục nội dung cụ thể dựa trên page_slug và section_key.
     * Nếu có nhiều mục với cùng section_key, nó sẽ trả về mục có item_order nhỏ nhất (hoặc 0).
     */
    public function getContentItem(string $pageSlug, string $sectionKey) {
        $sql = "SELECT id, content_type, content_value_text, is_visible 
                FROM page_content 
                WHERE page_slug = ? AND section_key = ? 
                ORDER BY item_order ASC 
                LIMIT 1";
        $row = pdo_query_one($sql, $pageSlug, $sectionKey);
        
        if ($row) {
            return [
                'id' => $row['id'],
                'type' => $row['content_type'],
                'text' => $row['content_value_text'],
                'is_visible' => (bool)$row['is_visible'] // Chuyển đổi sang boolean
            ];
        }
        return null;
    }
    /**
     * Cập nhật hoặc chèn một mục nội dung.
     * Nếu mục với page_slug, section_key, và item_order đã tồn tại, nó sẽ được cập nhật.
     * Nếu không, một mục mới sẽ được chèn.
     *
     * @param string $pageSlug Slug của trang.
     * @param string $sectionKey Khóa định danh của mục nội dung.
     * @param int $itemOrder Thứ tự của mục (thường là 0 cho các mục đơn lẻ).
     * @param string $contentType Loại nội dung (text, textarea, image_url, html, v.v.).
     * @param string $contentValueText Giá trị text của nội dung.
     * @param bool $isVisible Trạng thái hiển thị (true=1, false=0). Mặc định là true.
     * @return bool True nếu thành công, False nếu thất bại.
     */
    public function upsertContentItem(string $pageSlug, string $sectionKey, int $itemOrder, string $contentType, string $contentValueText, bool $isVisible = true): bool {
        // kiểm tra xem mục đã tồn tại chưa
        $sql_check = "SELECT id FROM page_content WHERE page_slug = ? AND section_key = ? AND item_order = ?";
        $existing_item = pdo_query_one($sql_check, $pageSlug, $sectionKey, $itemOrder);
        
        $visibleInt = $isVisible ? 1 : 0; // Chuyển boolean sang integer cho CSDL

        if ($existing_item) {
            // Cập nhật mục hiện có
            $sql_update = "UPDATE page_content 
                           SET content_type = ?, content_value_text = ?, is_visible = ?, updated_at = CURRENT_TIMESTAMP
                           WHERE id = ?";
            try {
                pdo_execute($sql_update, $contentType, $contentValueText, $visibleInt, $existing_item['id']);
                return true;
            } catch (PDOException $e) {
                error_log("Error updating page content: " . $e->getMessage());
                return false;
            }
        } else {
            // Chèn mục mới
            $sql_insert = "INSERT INTO page_content (page_slug, section_key, item_order, content_type, content_value_text, is_visible, created_at, updated_at) 
                           VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            try {
                pdo_execute($sql_insert, $pageSlug, $sectionKey, $itemOrder, $contentType, $contentValueText, $visibleInt);
                return true;
            } catch (PDOException $e) {
                error_log("Error inserting page content: " . $e->getMessage());
                return false;
            }
        }
    }
}
?> 