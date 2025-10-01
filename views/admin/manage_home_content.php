<?php
require_once __DIR__ . '/../../DAO/UserDAO.php';
require_once __DIR__ . '/../../DAO/PageContentDAO.php';
require_once __DIR__ . '/../../DAO/pages.php';

// Cấu hình upload hình ảnh
define('HOME_IMAGE_UPLOAD_DIR_SERVER', dirname(__DIR__, 3) . '/VINICA/layout/img/uploads/home/'); // Đường dẫn tuyệt đối trên server
define('HOME_IMAGE_UPLOAD_DIR_PUBLIC', '/VINICA/layout/img/uploads/home/');   
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Kiểm tra và tạo thư mục upload nếu chưa tồn tại
if (!is_dir(HOME_IMAGE_UPLOAD_DIR_SERVER)) {
    mkdir(HOME_IMAGE_UPLOAD_DIR_SERVER, 0775, true); // 0775 cho phép chủ sở hữu và group ghi, người khác đọc/execute
}

// Kiểm tra xác thực và quyền admin
$userDAO = new UserDAO();
$loggedInUser = null;
if (isset($_SESSION['user_id'])) {
    $loggedInUser = $userDAO->findById($_SESSION['user_id']);
}

if (!$loggedInUser) {
    header("Location: /VINICA/login");
    exit;
} elseif ($loggedInUser['role'] !== 'admin') {
    header("Location: /VINICA/admin-dashboard");
    exit;
}

$pageContentDAO = new PageContentDAO();
$pageSlug = 'home';

// Tải dữ liệu SEO
$currentPageDetails = get_page_details_by_slug($pageSlug);
if (!$currentPageDetails) {
    // Nếu không tìm thấy, khởi tạo với giá trị rỗng
    $currentPageDetails = ['title' => '', 'meta_description' => '', 'meta_keywords' => ''];
}

// Định nghĩa các section key và loại của chúng cho trang chủ
// Loại: 'text', 'textarea', 'image_url', 'html' (sẽ dùng TinyMCE cho html và textarea)
$homeSections = [
    'meta_title' => ['label' => 'Meta Title (SEO)', 'type' => 'text'],
    'meta_description' => ['label' => 'Meta Description (SEO)', 'type' => 'textarea'],
    'meta_keywords' => ['label' => 'Meta Keywords (SEO)', 'type' => 'text'],
    'hero_image_url' => ['label' => 'Hero Image', 'type' => 'image_url'],
    'intro_main_heading' => ['label' => 'Main Heading', 'type' => 'text'],
    'art_dining_heading' => ['label' => 'Heading "The Art of Fine Dining"', 'type' => 'text'],
    'art_dining_text' => ['label' => 'Content "The Art of Fine Dining"', 'type' => 'html'],
    'art_dining_image_url' => ['label' => 'Image "The Art of Fine Dining"', 'type' => 'image_url'],
    'events_heading' => ['label' => 'Heading "Private Events & Celebrations"', 'type' => 'text'],
    'events_text' => ['label' => 'Content "Private Events & Celebrations"', 'type' => 'html'],
    'events_image_url' => ['label' => 'Image "Private Events & Celebrations"', 'type' => 'image_url'],
    'diversity_heading' => ['label' => 'Heading "Diversity In Choices"', 'type' => 'text'],
    'diversity_text' => ['label' => 'Content "Diversity In Choices"', 'type' => 'html'],
    'diversity_menu_link_text' => ['label' => 'Text for "Check Menu" link', 'type' => 'text'],
    'diversity_menu_link_href' => ['label' => 'Link for "Check Menu" link', 'type' => 'text'],
    'diversity_image_url' => ['label' => 'Image "Diversity In Choices"', 'type' => 'image_url'],
    'signature_dishes_heading' => ['label' => 'Heading "Our Signature Dishes"', 'type' => 'text'],
    'signature_dishes_view_all_text' => ['label' => 'Text for "View Full Menu" button', 'type' => 'text'],
    'signature_dishes_view_all_href' => ['label' => 'Link for "View Full Menu" button', 'type' => 'text'],
    'our_story_heading' => ['label' => 'Heading "Our Story"', 'type' => 'text'],
    'our_story_text' => ['label' => 'Content "Our Story"', 'type' => 'html'],
    'our_story_image_url' => ['label' => 'Image "Our Story"', 'type' => 'image_url'],
    'our_story_learn_more_text' => ['label' => 'Text for "Learn More About Us" button', 'type' => 'text'],
    'our_story_learn_more_href' => ['label' => 'Link for "Learn More About Us" button', 'type' => 'text'],
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $successCount = 0;
    $errorCount = 0;
    $uploadErrors = [];

    // Tách trường SEO
    $seoTitle = $_POST['meta_title'] ?? ($currentPageDetails['title'] ?? '');
    $seoMetaDescription = $_POST['meta_description'] ?? ($currentPageDetails['meta_description'] ?? '');
    $seoMetaKeywords = $_POST['meta_keywords'] ?? ($currentPageDetails['meta_keywords'] ?? '');

    // Cập nhật chi tiết SEO trong bảng 'pages'
    if (update_page_seo_details($pageSlug, $seoTitle, $seoMetaDescription, $seoMetaKeywords)) {
        $successCount++;
    } else {
        $errorCount++;
        $uploadErrors[] = "Error updating SEO details for the page.";
    }

    // Lấy TẤT CẢ nội dung, bao gồm cả mục ẩn, để admin có thể quản lý trạng thái is_visible
    $currentContentRaw = $pageContentDAO->getContentByPageSlug($pageSlug, false);
    $currentContent = [];
    foreach ($currentContentRaw as $key => $items) {
        if (count($items) === 1) { // Giả sử item_order = 0 là chính
            $currentContent[$key] = $items[0];
        }
    }

    foreach ($homeSections as $sectionKey => $details) {
        // Bỏ qua trường SEO vì chúng đã được xử lý riêng
        if (in_array($sectionKey, ['meta_title', 'meta_description', 'meta_keywords'])) {
            continue;
        }

        $contentType = $details['type'];
        $contentValueToSave = ''; 
        $newImageUploaded = false;
        $isVisible = true; // Giá trị mặc định nếu không phải là mục có thể ẩn/hiện

        // Chỉ xử lý is_visible cho các mục không phải SEO (vì SEO không có trong page_content)
        if (!in_array($sectionKey, ['meta_title', 'meta_description', 'meta_keywords'])) {
            // Nếu checkbox được check, $_POST["{$sectionKey}_is_visible"] sẽ tồn tại (thường là "on")
            // Nếu không được check, nó sẽ không tồn tại trong $_POST.
            $isVisible = isset($_POST["{$sectionKey}_is_visible"]);
        }

        if ($contentType === 'image_url') {
            $postedUrlValue = $_POST[$sectionKey] ?? '';

            if (isset($_FILES[$sectionKey . '_file']) && $_FILES[$sectionKey . '_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$sectionKey . '_file'];
                if ($file['size'] > MAX_FILE_SIZE) {
                    $uploadErrors[] = "Error: File '" . htmlspecialchars($file['name']) . "' for item '" . htmlspecialchars($details['label']) . "' exceeds the maximum allowed size (2MB).";
                    $errorCount++;
                    continue; 
                }
                $fileMimeType = mime_content_type($file['tmp_name']);
                $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($fileMimeType, ALLOWED_MIME_TYPES) || !in_array($fileExtension, ALLOWED_EXTENSIONS)) {
                    $uploadErrors[] = "Error: File '" . htmlspecialchars($file['name']) . "' for item '" . htmlspecialchars($details['label']) . "' has an invalid format. Only JPG, PNG, GIF, WEBP are allowed.";
                    $errorCount++;
                    continue;
                }
                $newFileName = uniqid($sectionKey . '_', true) . '.' . $fileExtension;
                $destination = HOME_IMAGE_UPLOAD_DIR_SERVER . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $contentValueToSave = HOME_IMAGE_UPLOAD_DIR_PUBLIC . $newFileName;
                    $newImageUploaded = true;
                } else {
                    $uploadErrors[] = "Error: Unable to move uploaded file for item '" . htmlspecialchars($details['label']) . "'. Check directory permissions.";
                    $errorCount++;
                    continue; 
                }
            }

            if (!$newImageUploaded) {
                if (!empty($postedUrlValue)) { 
                    $isValidPostedValue = false;
                    if (strpos($postedUrlValue, 'http://') === 0 || strpos($postedUrlValue, 'https://') === 0) {
                        if (filter_var($postedUrlValue, FILTER_VALIDATE_URL)) {
                            $isValidPostedValue = true;
                        }
                    } elseif (is_string($postedUrlValue) && !empty(trim($postedUrlValue)) && $postedUrlValue[0] === '/') {
                        $isValidPostedValue = true;
                    }

                    if ($isValidPostedValue) {
                        $contentValueToSave = $postedUrlValue;
                    } else {
                        $uploadErrors[] = "Error: Invalid URL or path in text input for image item '" . htmlspecialchars($details['label']) . "'. Must be a full URL or an absolute path starting with '/'. Value: " . htmlspecialchars($postedUrlValue);
                        $errorCount++;
                        continue;
                    }
                } else { 
                    $existingItemData = $currentContent[$sectionKey] ?? null;
                    if ($existingItemData && isset($existingItemData['text'])) {
                        $existingDbValue = $existingItemData['text'];
                        $isValidExistingDbValue = false;
                        if (strpos($existingDbValue, 'http://') === 0 || strpos($existingDbValue, 'https://') === 0) {
                            if (filter_var($existingDbValue, FILTER_VALIDATE_URL)) {
                                $isValidExistingDbValue = true;
                            }
                        } elseif (is_string($existingDbValue) && !empty(trim($existingDbValue)) && $existingDbValue[0] === '/') {
                            $isValidExistingDbValue = true;
                        }

                        if ($isValidExistingDbValue) {
                            $contentValueToSave = $existingDbValue; 
                        } else {
                            $contentValueToSave = ''; 
                        }
                    } else {
                        $contentValueToSave = ''; 
                    }
                }
            }
        } elseif ($contentType === 'text' && strpos($sectionKey, '_href') !== false) {
            $postedHrefValue = $_POST[$sectionKey] ?? '';
            if (!empty($postedHrefValue)) {
                $isValidHref = false;
                if (strpos($postedHrefValue, 'http://') === 0 || strpos($postedHrefValue, 'https://') === 0) {
                        if (filter_var($postedHrefValue, FILTER_VALIDATE_URL)) $isValidHref = true;
                } elseif (is_string($postedHrefValue) && !empty(trim($postedHrefValue))) {
                    if ($postedHrefValue[0] === '/' || $postedHrefValue[0] === '#') $isValidHref = true;
                    elseif (strpos($postedHrefValue, 'mailto:') === 0) $isValidHref = true;
                    elseif (strpos($postedHrefValue, 'tel:') === 0) $isValidHref = true;
                }
                
                if ($isValidHref) {
                    $contentValueToSave = $postedHrefValue;
                } else {
                    $uploadErrors[] = "Error: Invalid URL, path, or URI for link item '" . htmlspecialchars($details['label']) . "'. Value: " . htmlspecialchars($postedHrefValue);
                    $errorCount++;
                    continue;
                }
            } else { // Submitted _href is empty
                $existingItemData = $currentContent[$sectionKey] ?? null;
                if ($existingItemData && isset($existingItemData['text']) && !empty(trim($existingItemData['text']))) {
                    // If there is an existing, non-empty value in the DB, use it
                    $contentValueToSave = $existingItemData['text'];
                } else {
                    // Otherwise, save the empty string
                    $contentValueToSave = ''; 
                }
            }
        } else { 
            $contentValueToSave = $_POST[$sectionKey] ?? '';
        }

        // Chỉ gọi upsert cho các mục trong page_content (không phải SEO)
        if (!in_array($sectionKey, ['meta_title', 'meta_description', 'meta_keywords'])) {
            if ($pageContentDAO->upsertContentItem($pageSlug, $sectionKey, 0, $contentType, $contentValueToSave, $isVisible)) {
                $successCount++;
            } else {
                $errorCount++;
                $uploadErrors[] = "Error updating content for item '" . htmlspecialchars($details['label']) . "'. Database operation failed.";
            }
        } // Kết thúc kiểm tra bỏ qua SEO fields cho upsert
    }

    if (!empty($uploadErrors)) {
        $_SESSION['error_message'] = implode("<br>", $uploadErrors);
    } elseif ($errorCount === 0 && $successCount > 0) {
        $_SESSION['success_message'] = "Home page content has been updated successfully ($successCount items).";
    } elseif ($errorCount > 0) {
            $_SESSION['error_message'] = "An error occurred while updating $errorCount items. $successCount items were updated successfully.";
    } else if ($successCount === 0 && $errorCount === 0 && empty($uploadErrors)){
        $_SESSION['info_message'] = "No changes were made.";
    }
}
// Lấy dữ liệu hiện tại để hiển thị trong form
$currentContentRaw = $pageContentDAO->getContentByPageSlug($pageSlug, false);
$currentContent = [];
foreach ($currentContentRaw as $key => $items) {
    if (count($items) === 1) { // Giả sử item_order = 0 là chính
        $currentContent[$key] = $items[0];
    }
}

// Hàm trợ giúp để lấy giá trị cho form, không escape HTML ở đây vì TinyMCE cần HTML nguyên bản
function getFormValue($contentArray, $key, $field = 'text', $default = '') {
    if (isset($contentArray[$key]) && isset($contentArray[$key][$field])) {
        return $contentArray[$key][$field]; // Không dùng htmlspecialchars ở đây
    }
    return $default;
}


$title = "Manage Home Page Content";
ob_start();
?>

<div class="container mt-4 admin-management-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Home Content</li>
        </ol>
    </nav>
    <h1 class="mb-4 page-title">Manage Home Page Content</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['info_message'])): ?>
        <div class="alert alert-info"><?php echo $_SESSION['info_message']; unset($_SESSION['info_message']); ?></div>
    <?php endif; ?>

    <form method="POST" action="/VINICA/admin-dashboard/manage_home_content" enctype="multipart/form-data">

        <?php foreach ($homeSections as $sectionKey => $details): ?>
            <div class="mb-3 form-section">
                <label for="<?php echo $sectionKey; ?>" class="form-label"><strong><?php echo htmlspecialchars($details['label']); ?></strong></label>
                
                <?php 
                $rawValue = '';
                $type = $details['type'];
                $currentIsVisible = true; // Mặc định là true cho các mục SEO hoặc nếu không tìm thấy

                if (in_array($sectionKey, ['meta_title', 'meta_description', 'meta_keywords'])) {
                    if ($sectionKey === 'meta_title') $rawValue = $currentPageDetails['title'] ?? '';
                    elseif ($sectionKey === 'meta_description') $rawValue = $currentPageDetails['meta_description'] ?? '';
                    elseif ($sectionKey === 'meta_keywords') $rawValue = $currentPageDetails['meta_keywords'] ?? '';
                } else {
                    // Lấy giá trị và trạng thái is_visible cho các mục không phải SEO
                    $rawValue = getFormValue($currentContent, $sectionKey, 'text');
                    // Giả sử item_order = 0 là chính, và $currentContent[$sectionKey] là một mảng các item
                    // Nếu $currentContent[$sectionKey] chỉ có một item, nó đã được gán ở trên
                    // Nếu $currentContent[$sectionKey] là mảng các items, lấy item đầu tiên (order 0)
                    $itemData = $currentContent[$sectionKey][0] ?? ($currentContent[$sectionKey] ?? null); 
                    if ($itemData && isset($itemData['is_visible'])) {
                        $currentIsVisible = (bool)$itemData['is_visible'];
                    }
                }
                
                $displayValue = $rawValue; // dữ liệu hiện tại

                // Kiểm tra dữ liệu hiện tại có hợp lệ không
                if ($type === 'image_url' || ($type === 'text' && strpos($sectionKey, '_href') !== false)) {
                    if (!empty($rawValue)) {
                        $isValidForDisplay = false;
                        if (strpos($rawValue, 'http://') === 0 || strpos($rawValue, 'https://') === 0) {
                            if (filter_var($rawValue, FILTER_VALIDATE_URL)) {
                                $isValidForDisplay = true;
                            }
                        } 
                        elseif (is_string($rawValue) && !empty(trim($rawValue))) { // Kiểm tra $rawValue[0]
                            if ($rawValue[0] === '/') { // Đường dẫn tuyệt đối là hợp lệ
                                $isValidForDisplay = true; 
                            }
                            if ($type === 'text' && strpos($sectionKey, '_href') !== false) { // Kiểm tra _href, cũng cho phép anchors, mailto, tel
                                if ($rawValue[0] === '#') $isValidForDisplay = true; 
                                if (strpos($rawValue, 'mailto:') === 0) $isValidForDisplay = true;
                                if (strpos($rawValue, 'tel:') === 0) $isValidForDisplay = true;
                            }
                        }
                        if (!$isValidForDisplay) {
                            $displayValue = ''; 
                        }
                    } else { // rawValue rỗng
                         $displayValue = ''; // Đảm bảo rawValue rỗng dẫn đến displayValue rỗng
                    }
                }

                // Xác định giá trị cho trường input text
                $inputFieldTextValue = $displayValue; // Mặc định là displayValue

                if (($type === 'image_url') || ($type === 'text' && strpos($sectionKey, '_href') !== false)) {
                    // Đối với trường input URL (trường URL ảnh, trường href):
                    // Chỉ điền với URL http/https.
                    // Nếu $displayValue là đường dẫn tuyệt đối, anchor, mailto, tel, etc., trường input phải rỗng.
                    if (!empty($inputFieldTextValue) && 
                        !(strpos($inputFieldTextValue, 'http://') === 0 || strpos($inputFieldTextValue, 'https://') === 0)
                       ) {
                        $inputFieldTextValue = '';
                    }
                } else if ($type === 'text') {
                    // Đối với trường 'text' tổng quát, $inputFieldTextValue phải là $rawValue (đây là $displayValue ở đây vì không có kiểm tra đặc biệt)
                    // Điều này đảm bảo rằng trường văn bản thông thường không bị vô tình xóa nếu chúng chứa chuỗi không phải là http.
                     $inputFieldTextValue = $rawValue; // Hoặc $displayValue, chúng giống nhau cho trường văn bản không phải là URL, không kiểm tra
                }
                // Đối với 'textarea' và 'html', chúng sẽ sử dụng $displayValue trực tiếp trong nội dung của chúng, không phải $inputFieldTextValue.
                ?>

                <?php if ($type === 'textarea' || $type === 'html'): ?>
                    <textarea class="form-control <?php echo ($type === 'html' ? 'tinymce-editor' : ''); ?>" id="<?php echo $sectionKey; ?>" name="<?php echo $sectionKey; ?>" rows="<?php echo ($type === 'html' ? '10' : '3'); ?>"><?php echo ($type === 'html' ? $displayValue : htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8')); ?></textarea>
                    <?php if ($type === 'html'): ?>
                        <small class="form-text text-muted">Trường này sử dụng trình soạn thảo văn bản đa năng.</small>
                    <?php endif; ?>
                <?php elseif ($type === 'image_url'): ?>
                    <div class="mb-2">
                        <label for="<?php echo $sectionKey; ?>_file" class="form-label"><small>Tải lên ảnh mới (Tùy chọn, tối đa 2MB, JPG/PNG/GIF/WEBP):</small></label>
                        <input type="file" class="form-control" id="<?php echo $sectionKey; ?>_file" name="<?php echo $sectionKey; ?>_file" accept=".jpg,.jpeg,.png,.gif,.webp">
                    </div>
                    <label for="<?php echo $sectionKey; ?>" class="form-label mt-1"><small>Hoặc nhập URL ảnh trực tiếp:</small></label>
                    <input type="url" class="form-control" id="<?php echo $sectionKey; ?>" name="<?php echo $sectionKey; ?>" value="<?php echo htmlspecialchars($inputFieldTextValue, ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://example.com/image.jpg">
                    <?php 
                    $canPreview = !empty($displayValue) && 
                                  (filter_var($displayValue, FILTER_VALIDATE_URL) || (is_string($displayValue) && !empty(trim($displayValue)) && $displayValue[0] === '/'));
                    if ($canPreview): 
                    ?>
                        <div class="mt-2">
                            <p><small>Ảnh hiện tại:</small></p>
                            <img src="<?php echo htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8'); ?>" alt="Xem trước <?php echo htmlspecialchars($details['label']); ?>" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                <?php elseif (strpos($sectionKey, '_href') !== false && $type === 'text'): // Các trường link href ?>
                     <input type="url" class="form-control" id="<?php echo $sectionKey; ?>" name="<?php echo $sectionKey; ?>" value="<?php echo htmlspecialchars($inputFieldTextValue, ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://example.com/page">
                     <small class="form-text text-muted">Vui lòng nhập một URL hợp lệ.</small>
                <?php else: // type 'text' ?>
                    <input type="text" class="form-control" id="<?php echo $sectionKey; ?>" name="<?php echo $sectionKey; ?>" value="<?php echo htmlspecialchars($inputFieldTextValue, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>

                <?php if (!in_array($sectionKey, ['meta_title', 'meta_description', 'meta_keywords'])): ?>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="<?php echo $sectionKey; ?>_is_visible" id="<?php echo $sectionKey; ?>_is_visible" <?php echo $currentIsVisible ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="<?php echo $sectionKey; ?>_is_visible">
                            Hiển thị mục này
                        </label>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary btn-lg">Lưu thay đổi</button>
    </form>
</div>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/vjtkoqa879nbtp28a4qscoij49z7lcy3xc7olzwgvach3ph0/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            plugins: 'code image link lists media table wordcount preview fullscreen help',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | ' +
                     'bullist numlist outdent indent | link image media table | code preview fullscreen | help',
            menubar: 'file edit view insert format tools table help',
            height: 400,
        });

        // JavaScript để xem trước ảnh khi chọn file
        document.querySelectorAll('input[type="file"]').forEach(function(fileInput) {
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    // Tìm thẻ img xem trước tương ứng
                    // Giả sử thẻ img nằm trong cùng div.mt-2 với input file, hoặc có một cấu trúc DOM nhất quán
                    // Ví dụ: input file có id là 'sectionKey_file', ảnh xem trước nằm trong div ngay sau đó có img tag
                    let previewImage = null;
                    const imageInputId = event.target.id.replace('_file', ''); // Lấy sectionKey
                    const imageValueInput = document.getElementById(imageInputId);
                    
                    // Tìm thẻ img xem trước liên quan đến input URL này
                    // Cấu trúc: div.mb-3 -> div.mt-2 -> img
                    let parentDiv = imageValueInput.closest('.mb-3.form-section');
                    if (parentDiv) {
                        previewImage = parentDiv.querySelector('.mt-2 img');
                    }

                    reader.onload = function(e) {
                        if (previewImage) {
                            previewImage.src = e.target.result;
                            previewImage.style.display = 'block'; // Đảm bảo ảnh được hiển thị
                        } else {
                            // Nếu không tìm thấy thẻ img hiện có, có thể tạo mới (tùy chọn)
                            // Hoặc đơn giản là không làm gì nếu không có chỗ xem trước được định nghĩa sẵn
                            console.warn("Không tìm thấy thẻ img xem trước cho: " + imageInputId);
                        }
                        // Cập nhật hoặc làm nổi bật trường input URL để người dùng biết ảnh sẽ thay đổi
                        // Ví dụ: imageValueInput.value = "Tệp đã chọn: " + file.name; // Chỉ mang tính thông báo
                        // Không nên gán file.name trực tiếp vì đó không phải là URL cuối cùng
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../main.php'; 
?> 