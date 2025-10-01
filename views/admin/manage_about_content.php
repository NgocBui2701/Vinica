<?php
require_once __DIR__ . '/../../DAO/UserDAO.php';
require_once __DIR__ . '/../../DAO/PageContentDAO.php';
require_once __DIR__ . '/../../DAO/pages.php';

// Cấu hình upload hình ảnh cho trang Giới Thiệu
define('ABOUT_IMAGE_UPLOAD_DIR_SERVER', dirname(__DIR__, 3) . '/VINICA/layout/img/uploads/about/'); // Đường dẫn tuyệt đối trên server
define('ABOUT_IMAGE_UPLOAD_DIR_PUBLIC', '/VINICA/layout/img/uploads/about/');   
define('MAX_FILE_SIZE_ABOUT', 2 * 1024 * 1024); // 2MB (có thể dùng chung MAX_FILE_SIZE nếu giống nhau)
define('ALLOWED_MIME_TYPES_ABOUT', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']); // (có thể dùng chung)
define('ALLOWED_EXTENSIONS_ABOUT', ['jpg', 'jpeg', 'png', 'gif', 'webp']); // (có thể dùng chung)

// Kiểm tra và tạo thư mục upload nếu chưa tồn tại
if (!is_dir(ABOUT_IMAGE_UPLOAD_DIR_SERVER)) {
    mkdir(ABOUT_IMAGE_UPLOAD_DIR_SERVER, 0775, true);
}

// Kiểm tra xác thực và quyền admin
$userDAO = new UserDAO();
$loggedInUser = null;
if (isset($_SESSION['user_id'])) {
    $loggedInUser = $userDAO->findById($_SESSION['user_id']);
}

if (!$loggedInUser) {
    $_SESSION['error_message'] = "Please login to continue.";
    header("Location: /VINICA/login");
    exit;
} elseif ($loggedInUser['role'] !== 'admin') {
    $_SESSION['error_message'] = "You do not have permission to access this page.";
    header("Location: /VINICA/admin-dashboard");
    exit;
}

$pageContentDAO = new PageContentDAO();
$pageSlug = 'about'; // Thay đổi slug thành 'about'

// Tải dữ liệu SEO
$currentPageDetails = get_page_details_by_slug($pageSlug);
if (!$currentPageDetails) {
    $currentPageDetails = ['title' => '', 'meta_description' => '', 'meta_keywords' => ''];
}

// Định nghĩa các section key và loại của chúng cho trang Giới Thiệu
$aboutSections = [
    'about_meta_title' => ['label' => 'Meta Title (SEO)', 'type' => 'text'],
    'about_meta_description' => ['label' => 'Meta Description (SEO)', 'type' => 'textarea'],
    'about_meta_keywords' => ['label' => 'Meta Keywords (SEO)', 'type' => 'text'],
    
    'about_hero_image_url' => ['label' => 'Hero Image (Background)', 'type' => 'image_url'],
    'about_page_main_heading' => ['label' => 'Page Main Heading (e.g., \\"In Our Restaurant\\")', 'type' => 'text'],
    'about_page_subtitle' => ['label' => 'Page Subtitle', 'type' => 'html'],

    'about_story_image_url' => ['label' => 'Story Section - Image', 'type' => 'image_url'],
    'about_story_heading' => ['label' => 'Story Section - Heading', 'type' => 'text'],
    'about_story_text' => ['label' => 'Story Section - Content', 'type' => 'html'],

    'about_ambience_main_heading' => ['label' => 'Ambience Section - Main Heading', 'type' => 'text'],
    
    'about_ambience_gh_image_url' => ['label' => 'Ambience: Grand Hall - Image', 'type' => 'image_url'],
    'about_ambience_gh_name' => ['label' => 'Ambience: Grand Hall - Name', 'type' => 'text'],
    'about_ambience_gh_subtitle' => ['label' => 'Ambience: Grand Hall - Subtitle', 'type' => 'text'],
    'about_ambience_gh_text' => ['label' => 'Ambience: Grand Hall - Description', 'type' => 'html'],

    'about_ambience_tv_image_url' => ['label' => 'Ambience: Terrace View - Image', 'type' => 'image_url'],
    'about_ambience_tv_name' => ['label' => 'Ambience: Terrace View - Name', 'type' => 'text'],
    'about_ambience_tv_subtitle' => ['label' => 'Ambience: Terrace View - Subtitle', 'type' => 'text'],
    'about_ambience_tv_text' => ['label' => 'Ambience: Terrace View - Description', 'type' => 'html'],

    'about_ambience_ll_image_url' => ['label' => 'Ambience: Lobby Lounge - Image', 'type' => 'image_url'],
    'about_ambience_ll_name' => ['label' => 'Ambience: Lobby Lounge - Name', 'type' => 'text'],
    'about_ambience_ll_subtitle' => ['label' => 'Ambience: Lobby Lounge - Subtitle', 'type' => 'text'],
    'about_ambience_ll_text' => ['label' => 'Ambience: Lobby Lounge - Description', 'type' => 'html'],

    'about_ambience_pdr_image_url' => ['label' => 'Ambience: Private Dining Room - Image', 'type' => 'image_url'],
    'about_ambience_pdr_name' => ['label' => 'Ambience: Private Dining Room - Name', 'type' => 'text'],
    'about_ambience_pdr_subtitle' => ['label' => 'Ambience: Private Dining Room - Subtitle', 'type' => 'text'],
    'about_ambience_pdr_text' => ['label' => 'Ambience: Private Dining Room - Description', 'type' => 'html'],

    'about_ambience_gc_image_url' => ['label' => 'Ambience: Garden Courtyard - Image', 'type' => 'image_url'],
    'about_ambience_gc_name' => ['label' => 'Ambience: Garden Courtyard - Name', 'type' => 'text'],
    'about_ambience_gc_subtitle' => ['label' => 'Ambience: Garden Courtyard - Subtitle', 'type' => 'text'],
    'about_ambience_gc_text' => ['label' => 'Ambience: Garden Courtyard - Description', 'type' => 'html'],

    'about_team_main_heading' => ['label' => 'Team Section - Main Heading', 'type' => 'text'],
    'about_team_intro' => ['label' => 'Team Section - Introduction Text', 'type' => 'html'],

    'about_team_tm1_image_url' => ['label' => 'Team: Member 1 - Image', 'type' => 'image_url'],
    'about_team_tm1_name' => ['label' => 'Team: Member 1 - Name', 'type' => 'text'],
    'about_team_tm1_role_or_id' => ['label' => 'Team: Member 1 - Role/ID', 'type' => 'text'],

    'about_team_tm2_image_url' => ['label' => 'Team: Member 2 - Image', 'type' => 'image_url'],
    'about_team_tm2_name' => ['label' => 'Team: Member 2 - Name', 'type' => 'text'],
    'about_team_tm2_role_or_id' => ['label' => 'Team: Member 2 - Role/ID', 'type' => 'text'],

    'about_team_tm3_image_url' => ['label' => 'Team: Member 3 - Image', 'type' => 'image_url'],
    'about_team_tm3_name' => ['label' => 'Team: Member 3 - Name', 'type' => 'text'],
    'about_team_tm3_role_or_id' => ['label' => 'Team: Member 3 - Role/ID', 'type' => 'text'],
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $successCount = 0;
    $errorCount = 0;
    $uploadErrors = [];

    $seoTitle = $_POST['about_meta_title'] ?? ($currentPageDetails['title'] ?? '');
    $seoMetaDescription = $_POST['about_meta_description'] ?? ($currentPageDetails['meta_description'] ?? '');
    $seoMetaKeywords = $_POST['about_meta_keywords'] ?? ($currentPageDetails['meta_keywords'] ?? '');

    if (update_page_seo_details($pageSlug, $seoTitle, $seoMetaDescription, $seoMetaKeywords)) {
        $successCount++;
    } else {
        $errorCount++;
        $uploadErrors[] = "Error updating SEO details for the About page.";
    }

    $currentContentRaw = $pageContentDAO->getContentByPageSlug($pageSlug, false);
    $currentContent = [];
    foreach ($currentContentRaw as $key => $items) {
        if (count($items) === 1) {
            $currentContent[$key] = $items[0];
        }
    }

    foreach ($aboutSections as $sectionKey => $details) {
        if (in_array($sectionKey, ['about_meta_title', 'about_meta_description', 'about_meta_keywords'])) {
            continue;
        }

        $contentType = $details['type'];
        $contentValueToSave = ''; 
        $newImageUploaded = false;
        $isVisible = isset($_POST["{$sectionKey}_is_visible"]);

        if ($contentType === 'image_url') {
            $postedUrlValue = $_POST[$sectionKey] ?? '';

            if (isset($_FILES[$sectionKey . '_file']) && $_FILES[$sectionKey . '_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$sectionKey . '_file'];
                if ($file['size'] > MAX_FILE_SIZE_ABOUT) {
                    $uploadErrors[] = "Error: File '" . htmlspecialchars($file['name']) . "' for item '" . htmlspecialchars($details['label']) . "' exceeds the maximum allowed size (2MB).";
                    $errorCount++;
                    continue; 
                }
                $fileMimeType = mime_content_type($file['tmp_name']);
                $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($fileMimeType, ALLOWED_MIME_TYPES_ABOUT) || !in_array($fileExtension, ALLOWED_EXTENSIONS_ABOUT)) {
                    $uploadErrors[] = "Error: File '" . htmlspecialchars($file['name']) . "' for item '" . htmlspecialchars($details['label']) . "' has an invalid format. Only JPG, PNG, GIF, WEBP are allowed.";
                    $errorCount++;
                    continue;
                }
                $newFileName = uniqid(str_replace('about_', '', $sectionKey) . '_', true) . '.' . $fileExtension;
                $destination = ABOUT_IMAGE_UPLOAD_DIR_SERVER . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $contentValueToSave = ABOUT_IMAGE_UPLOAD_DIR_PUBLIC . $newFileName;
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
            } else {
                $contentValueToSave = ''; 
            }
        } else { 
            $contentValueToSave = $_POST[$sectionKey] ?? '';
        }

        if (!in_array($sectionKey, ['about_meta_title', 'about_meta_description', 'about_meta_keywords'])) {
            if ($pageContentDAO->upsertContentItem($pageSlug, $sectionKey, 0, $contentType, $contentValueToSave, $isVisible)) {
                $successCount++;
            } else {
                $errorCount++;
                $uploadErrors[] = "Error updating content for item '" . htmlspecialchars($details['label']) . "'. Database operation failed.";
            }
        }
    }

    if (!empty($uploadErrors)) {
        $_SESSION['error_message'] = implode("<br>", $uploadErrors);
    } elseif ($errorCount === 0 && $successCount > 0) {
        $_SESSION['success_message'] = "About page content has been updated successfully ($successCount items).";
    } elseif ($errorCount > 0) {
        $_SESSION['error_message'] = "An error occurred while updating $errorCount items. $successCount items were updated successfully.";
    } else if ($successCount === 0 && $errorCount === 0 && empty($uploadErrors)){
        $_SESSION['info_message'] = "No changes were made to the About page content.";
    }
    // Sau khi POST, nên chuyển hướng để tránh gửi lại form khi refresh
    // header("Location: " . $_SERVER['REQUEST_URI']); // Chuyển hướng về chính trang này
    // exit;
}

$currentContentRaw = $pageContentDAO->getContentByPageSlug($pageSlug, false);
$currentContent = [];
foreach ($currentContentRaw as $key => $items) {
    if (count($items) === 1) {
        $currentContent[$key] = $items[0];
    }
}

function getFormValueAbout($contentArray, $key, $field = 'text', $default = '') {
    if (isset($contentArray[$key]) && isset($contentArray[$key][$field])) {
        return $contentArray[$key][$field];
    }
    return $default;
}

$title = "Manage About Page Content";
ob_start();
?>

<div class="container mt-4 admin-management-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/VINICA/admin-dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage About Content</li>
        </ol>
    </nav>
    <h1 class="mb-4 page-title">Manage About Page Content</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['info_message'])): ?>
        <div class="alert alert-info"><?php echo $_SESSION['info_message']; unset($_SESSION['info_message']); ?></div>
    <?php endif; ?>

    <form method="POST" action="/VINICA/admin-dashboard/manage_about_content" enctype="multipart/form-data">

        <?php foreach ($aboutSections as $sectionKey => $details): ?>
            <div class="mb-3 form-section">
                <label for="<?php echo $sectionKey; ?>" class="form-label"><strong><?php echo htmlspecialchars($details['label']); ?></strong></label>
                
                <?php 
                $rawValue = '';
                $type = $details['type'];
                $currentIsVisible = true;

                if (in_array($sectionKey, ['about_meta_title', 'about_meta_description', 'about_meta_keywords'])) {
                    if ($sectionKey === 'about_meta_title') $rawValue = $currentPageDetails['title'] ?? '';
                    elseif ($sectionKey === 'about_meta_description') $rawValue = $currentPageDetails['meta_description'] ?? '';
                    elseif ($sectionKey === 'about_meta_keywords') $rawValue = $currentPageDetails['meta_keywords'] ?? '';
                } else {
                    // For non-SEO fields, get value from $currentContent
                    // It is assumed that $currentContent[$sectionKey] holds the single item 
                    // because of the processing loop: foreach ($currentContentRaw as $key => $items) ... $currentContent[$key] = $items[0];
                    $itemData = $currentContent[$sectionKey] ?? null;
                    if ($itemData) {
                        $rawValue = $itemData['text'] ?? ''; // Assuming 'text' field holds the content_value_text
                        $currentIsVisible = (bool)($itemData['is_visible'] ?? true);
                    } else {
                        $rawValue = ''; // Default to empty if no data found
                        $currentIsVisible = true; // Default to visible if no data
                    }
                }
                
                $displayValue = $rawValue;
                $inputFieldTextValue = $rawValue;


                if (($type === 'image_url') || ($type === 'text' && strpos($sectionKey, '_href') !== false)) {
                    if (!empty($rawValue)) {
                        $isValidForDisplay = false;
                        if (strpos($rawValue, 'http://') === 0 || strpos($rawValue, 'https://') === 0) {
                            if (filter_var($rawValue, FILTER_VALIDATE_URL)) $isValidForDisplay = true;
                        } 
                        elseif (is_string($rawValue) && !empty(trim($rawValue)) && $rawValue[0] === '/') {
                            $isValidForDisplay = true; 
                        }
                        if ($type === 'text' && strpos($sectionKey, '_href') !== false) {
                            if ($rawValue[0] === '#') $isValidForDisplay = true; 
                            if (strpos($rawValue, 'mailto:') === 0) $isValidForDisplay = true;
                            if (strpos($rawValue, 'tel:') === 0) $isValidForDisplay = true;
                        }
                        if (!$isValidForDisplay) $displayValue = '';
                    } else {
                         $displayValue = '';
                    }

                    $inputFieldTextValue = $displayValue;
                    if (!empty($inputFieldTextValue) && !(strpos($inputFieldTextValue, 'http://') === 0 || strpos($inputFieldTextValue, 'https://') === 0)) {
                        $inputFieldTextValue = '';
                    }
                } else if ($type === 'text') {
                     $inputFieldTextValue = $rawValue; 
                }
                ?>

                <?php if ($type === 'textarea' || $type === 'html'): ?>
                    <textarea class="form-control <?php echo ($type === 'html' ? 'tinymce-editor' : ''); ?>" id="<?php echo $sectionKey; ?>" name="<?php echo $sectionKey; ?>" rows="<?php echo ($type === 'html' ? '10' : '3'); ?>"><?php echo ($type === 'html' ? $displayValue : htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8')); ?></textarea>
                    <?php if ($type === 'html'): ?>
                        <small class="form-text text-muted">This field uses a rich text editor.</small>
                    <?php endif; ?>
                <?php elseif ($type === 'image_url'): ?>
                    <div class="mb-2">
                        <label for="<?php echo $sectionKey; ?>_file" class="form-label"><small>Upload new image (Optional, max 2MB, JPG/PNG/GIF/WEBP):</small></label>
                        <input type="file" class="form-control" id="<?php echo $sectionKey; ?>_file" name="<?php echo $sectionKey; ?>_file" accept=".jpg,.jpeg,.png,.gif,.webp">
                    </div>
                    <label for="<?php echo $sectionKey; ?>" class="form-label mt-1"><small>Or enter image URL directly:</small></label>
                    <input type="url" class="form-control" id="<?php echo $sectionKey; ?>" name="<?php echo $sectionKey; ?>" value="<?php echo htmlspecialchars($inputFieldTextValue, ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://example.com/image.jpg">
                    <?php 
                    $canPreview = !empty($displayValue) && 
                                  (filter_var($displayValue, FILTER_VALIDATE_URL) || (is_string($displayValue) && !empty(trim($displayValue)) && $displayValue[0] === '/'));
                    if ($canPreview): 
                    ?>
                        <div class="mt-2">
                            <p><small>Current image:</small></p>
                            <img src="<?php echo htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8'); ?>" alt="Preview <?php echo htmlspecialchars($details['label']); ?>" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                <?php elseif (strpos($sectionKey, '_href') !== false && $type === 'text'): ?>
                     <input type="url" class="form-control" id="<?php echo $sectionKey; ?>" name="<?php echo $sectionKey; ?>" value="<?php echo htmlspecialchars($inputFieldTextValue, ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://example.com/page">
                     <small class="form-text text-muted">Please enter a valid URL.</small>
                <?php else: ?>
                    <input type="text" class="form-control" id="<?php echo $sectionKey; ?>" name="<?php echo $sectionKey; ?>" value="<?php echo htmlspecialchars($inputFieldTextValue, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>

                <?php if (!in_array($sectionKey, ['about_meta_title', 'about_meta_description', 'about_meta_keywords'])): ?>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="<?php echo $sectionKey; ?>_is_visible" id="<?php echo $sectionKey; ?>_is_visible" <?php echo $currentIsVisible ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="<?php echo $sectionKey; ?>_is_visible">
                            Display this item
                        </label>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
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

        document.querySelectorAll('input[type="file"]').forEach(function(fileInput) {
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    let previewImage = null;
                    const imageInputId = event.target.id.replace('_file', '');
                    const imageValueInput = document.getElementById(imageInputId);
                    
                    let parentDiv = imageValueInput.closest('.mb-3.form-section');
                    if (parentDiv) {
                        previewImage = parentDiv.querySelector('.mt-2 img');
                    }

                    reader.onload = function(e) {
                        if (previewImage) {
                            previewImage.src = e.target.result;
                            previewImage.style.display = 'block';
                        } else {
                            const imgContainer = imageValueInput.closest('.form-section').querySelector('input[type="url"]').parentElement;
                            if(imgContainer){
                                let newPreviewDiv = imgContainer.querySelector('.mt-2');
                                if(!newPreviewDiv) {
                                    newPreviewDiv = document.createElement('div');
                                    newPreviewDiv.className = 'mt-2';
                                    const pTag = document.createElement('p');
                                    const smallTag = document.createElement('small');
                                    smallTag.textContent = 'Current image:';
                                    pTag.appendChild(smallTag);
                                    newPreviewDiv.appendChild(pTag);
                                    const newImg = document.createElement('img');
                                    newImg.style.maxWidth = '200px';
                                    newImg.style.maxHeight = '150px';
                                    newImg.style.border = '1px solid #ddd';
                                    newImg.style.objectFit = 'cover';
                                    newImg.alt = "Preview " + (parentDiv.querySelector('label strong') ? parentDiv.querySelector('label strong').textContent : '');
                                    newPreviewDiv.appendChild(newImg);
                                    imgContainer.appendChild(newPreviewDiv);
                                    previewImage = newImg;
                                } else {
                                   previewImage = newPreviewDiv.querySelector('img');
                                }
                                if(previewImage) {
                                   previewImage.src = e.target.result;
                                   previewImage.style.display = 'block';
                                }
                            }
                        }
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